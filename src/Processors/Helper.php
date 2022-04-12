<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Helpers\Arr;
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

        return $this->stub(Stubs::PAGE, compact('class', 'content'));
    }

    protected function methods(): array
    {
        $methods = [];

        foreach ($this->getMethods() as $method) {
            if ($method->isConstructor() || $method->isDestructor() || $method->isDeprecated() || $method->isAbstract()) {
                continue;
            }

            $methods[] = $this->method($method);
        }

        return $methods;
    }

    protected function method(ReflectionMethod $reflection): string
    {
        $class   = $reflection->class;
        $method  = $reflection->getName();
        $summary = $this->getSummary($reflection);
        $example = $this->getExample($reflection);

        return $this->stub(Stubs::BLOCK, compact('class', 'method', 'summary', 'example'));
    }

    protected function getSummary(ReflectionMethod $method): string
    {
        return (string) $this->docBlock($method)?->getSummary();
    }

    protected function getExample(ReflectionMethod $method): string
    {
        if ($code = $this->docBlock($method)?->getDescription()?->getBodyTemplate()) {
            return $this->stub(Stubs::EXAMPLE, compact('code'));
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
        return Reflection::resolve($this->class::getFacadeRoot())->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    protected function stub(Stubs $stub, array $values): string
    {
        return Stub::replace(__DIR__ . '/../../stubs/' . $stub->value, $values);
    }
}
