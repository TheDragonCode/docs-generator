<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\TemplateDto;
use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Facade;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Instance;
use DragonCode\Support\Facades\Instances\Reflection;
use DragonCode\Support\Facades\Tools\Stub;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;

/**
 * @property \DragonCode\Support\Facades\Facade|\Illuminate\Support\Facades\Facade|string $class
 */
abstract class Processor
{
    use Makeable;

    protected DocBlockFactory $doc;

    public function __construct(
        protected string $class
    ) {
        $this->doc = DocBlockFactory::createInstance();
    }

    abstract public function get(): string;

    abstract protected function method(ReflectionMethod $reflection): string;

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
