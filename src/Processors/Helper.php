<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\TemplateDto;
use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Facade;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Instance;
use DragonCode\Support\Facades\Instances\Reflection;
use DragonCode\Support\Facades\Tools\Stub;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;

/**
 * @property \DragonCode\Support\Facades\Facade|\Illuminate\Support\Facades\Facade|string $class
 */
class Helper
{
    use Makeable;

    protected DocBlockFactory $doc;

    public function __construct(
        protected string $class
    ) {
        $this->doc = DocBlockFactory::createInstance();
    }

    public function get(): string
    {
        $class = $this->class;

        $content = Arr::of($this->methods())->implode('');

        return $this->stub(Stubs::CLASS_STUB, new TemplateDto(compact('class', 'content')));
    }

    protected function methods(): array
    {
        $methods = [];

        foreach ($this->getMethods() as $method) {
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

        return $this->stub(Stubs::METHOD_STUB, new TemplateDto(compact('method', 'summary', 'code', 'example')));
    }

    protected function getSummary(ReflectionMethod $method): string
    {
        return (string) $this->docBlock($method)?->getSummary();
    }

    protected function getCallCode(ReflectionMethod $reflection): string
    {
        $class  = $this->class;
        $method = $reflection->getName();

        $stub = $this->isFacade() || $reflection->isStatic() ? Stubs::CODE_STATIC_STUB : Stubs::CODE_DYNAMIC_STUB;

        return $this->stub($stub, new TemplateDto(compact('class', 'method')));
    }

    protected function getExample(ReflectionMethod $method): string
    {
        if ($example = $this->docBlock($method)?->getDescription()?->getBodyTemplate()) {
            return $this->stub(Stubs::EXAMPLE_STUB, new TemplateDto(compact('example')));
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

    /**
     * @return ReflectionMethod[]
     */
    protected function getMethods(): array
    {
        $root = $this->isFacade() ? $this->class::getFacadeRoot() : $this->class;

        return Reflection::resolve($root)->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    protected function stub(Stubs $stub, TemplateDto $dto): string
    {
        return Stub::replace(__DIR__ . '/../../stubs/' . $stub->value, $dto->toArray());
    }

    protected function isFacade(): bool
    {
        return Instance::of($this->class, [Facade::class, '\Illuminate\Support\Facades\Facade']);
    }
}
