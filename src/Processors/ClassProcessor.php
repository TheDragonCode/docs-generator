<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\TemplateDto;
use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\Support\Facades\Helpers\Arr;
use phpDocumentor\Reflection\DocBlock;
use ReflectionMethod;

class ClassProcessor extends Processor
{
    public function get(): string
    {
        $class = $this->class;

        $content = Arr::of($this->methods())->implode('');

        return $this->stub(Stubs::CLASS_STUB, new TemplateDto(compact('class', 'content')));
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
}
