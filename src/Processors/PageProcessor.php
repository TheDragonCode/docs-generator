<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\Template;
use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\Support\Facades\Facade;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Reflection;
use phpDocumentor\Reflection\DocBlock;
use ReflectionMethod;

class PageProcessor extends Processor
{
    public function get(): string
    {
        $class = $this->file->getShowNamespace();

        $content = $this->getContent($this->file->getNamespace());

        return $this->stub(Stubs::CLASS_STUB, new Template(compact('class', 'content')));
    }

    protected function methods(string $namespace): array
    {
        $methods = [];

        foreach ($this->getMethods($namespace) as $method) {
            if ($method->isConstructor() || $method->isDestructor() || $method->isDeprecated() || $method->isAbstract() || $method->isInternal()) {
                continue;
            }

            if (Str::startsWith($method->getName(), '__')) {
                continue;
            }

            $methods[] = $this->method($method, $namespace);
        }

        return $methods;
    }

    protected function method(ReflectionMethod $reflection, string $namespace): string
    {
        $method  = $reflection->getName();
        $summary = $this->getSummary($reflection);
        $code    = $this->getCallCode($reflection, $namespace);
        $example = $this->getExample($reflection);

        return $this->stub(Stubs::METHOD_STUB, new Template(compact('method', 'summary', 'code', 'example')));
    }

    /**
     * @param Facade|string $namespace
     *
     * @return array<ReflectionMethod>
     */
    protected function getMethods(Facade|string $namespace): array
    {
        $root = $this->isFacade($namespace) ? $namespace::getFacadeRoot() : $namespace;

        $methods = Reflection::resolve($root)->getMethods(ReflectionMethod::IS_PUBLIC);

        return Arr::of($methods)
            ->renameKeys(static fn (int $key, ReflectionMethod $method) => $method->getName())
            ->ksort()
            ->toArray();
    }

    protected function getContent(string $namespace): string
    {
        return Arr::of($this->methods($namespace))->implode('');
    }

    protected function getSummary(ReflectionMethod $method): string
    {
        return (string) $this->docBlock($method)?->getSummary();
    }

    protected function getCallCode(ReflectionMethod $reflection, string $class): string
    {
        $method = $reflection->getName();

        $stub = $this->isFacade($class) || $reflection->isStatic() ? Stubs::CODE_STATIC_STUB : Stubs::CODE_DYNAMIC_STUB;

        return $this->stub($stub, new Template(compact('class', 'method')));
    }

    protected function getExample(ReflectionMethod $method): string
    {
        if ($example = $this->docBlock($method)?->getDescription()?->getBodyTemplate()) {
            return $this->stub(Stubs::EXAMPLE_STUB, new Template(compact('example')));
        }

        return '';
    }

    protected function docBlock(ReflectionMethod $method): ?DocBlock
    {
        if ($doc = $method->getDocComment()) {
            return $this->doc->create($doc);
        }

        return null;
    }
}
