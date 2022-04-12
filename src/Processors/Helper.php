<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Templates\Block;
use DragonCode\DocsGenerator\Templates\Page;
use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Reflection;
use JetBrains\PhpStorm\Pure;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;

class Helper
{
    use Makeable;

    protected DocBlockFactory $doc;

    #[Pure]
    public function __construct(
        protected string $path,
        protected string $header
    ) {
        $this->doc = DocBlockFactory::createInstance();
    }

    public function get(): string
    {
        return Page::make($this->header, Arr::of($this->methods())->implode(''));
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

    protected function method(ReflectionMethod $method): string
    {
        $summary     = $this->getSummary($method);
        $description = $this->getDescription($method);

        return Block::make($method->getName(), $summary . PHP_EOL . PHP_EOL . $description);
    }

    protected function getSummary(ReflectionMethod $method): string
    {
        return $this->doc->create($method->getDocComment())->getSummary();
    }

    protected function getDescription(ReflectionMethod $method): ?string
    {
        return $this->doc->create($method->getDocComment())->getDescription()->getBodyTemplate();
    }

    /**
     * @return ReflectionMethod[]
     */
    protected function getMethods(): array
    {
        return Reflection::resolve($this->resolveClassname())->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    protected function resolveClassname(): string
    {
        return Str::of($this->header)
            ->replace('/', '\\')
            ->prepend('DragonCode\\Support\\');
    }
}
