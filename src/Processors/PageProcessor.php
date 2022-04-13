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
        $class = $this->value;

        $content = $this->getContent();

        return $this->stub(Stubs::CLASS_STUB, new Template(compact('class', 'content')));
    }

    protected function methods(Facade|string $namespace): array
    {
        $methods = [];

        foreach ($this->getMethods($namespace) as $method) {
            if ($method->isConstructor() || $method->isDestructor() || $method->isDeprecated() || $method->isAbstract() || $method->isInternal()) {
                continue;
            }

            if (Str::startsWith($method->getName(), '__')) {
                continue;
            }

            $methods[] = $this->method($method);
        }

        return $methods;
    }

    protected function method(ReflectionMethod $reflection): string
    {
        $method  = $reflection->getName();
        $summary = $this->getSummary($reflection);
        $code    = $this->getCallCode($reflection);
        $example = $this->getExample($reflection);

        return $this->stub(Stubs::METHOD_STUB, new Template(compact('method', 'summary', 'code', 'example')));
    }

    /**
     * @param Facade|string $namespace
     *
     * @return ReflectionMethod[]
     */
    protected function getMethods(Facade|string $namespace): array
    {
        $root = $this->isFacade($namespace) ? $namespace::getFacadeRoot() : $namespace;

        return Reflection::resolve($root)->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    protected function getContent(Facade|string $namespace): string
    {
        return Arr::of($this->methods($namespace))->implode('');
    }

    protected function getSummary(ReflectionMethod $method): string
    {
        return (string) $this->docBlock($method)?->getSummary();
    }

    protected function getCallCode(ReflectionMethod $reflection): string
    {
        $class  = $this->value;
        $method = $reflection->getName();

        $stub = $this->isFacade() || $reflection->isStatic() ? Stubs::CODE_STATIC_STUB : Stubs::CODE_DYNAMIC_STUB;

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
