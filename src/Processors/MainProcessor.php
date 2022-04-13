<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\TemplateDto;
use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\Support\Facades\Helpers\Arr;
use ReflectionMethod;

class MainProcessor extends Processor
{
    public function get(): string
    {
        $title   = 'aaa';
        $vendor  = 'bbb';
        $package = 'ccc';
        $bash    = 'ddd';

        $content = Arr::of($this->methods())
            ->map(fn (string $method) => trim($method))
            ->implode(PHP_EOL);

        return $this->stub(Stubs::MAIN_STUB, new TemplateDto(compact('title', 'vendor', 'package', 'bash', 'content')));
    }

    protected function method(ReflectionMethod $reflection): string
    {
        $title = 1;
        $link  = 2;

        return $this->stub(Stubs::LIST_ITEM_STUB, new TemplateDto(compact('title', 'link')));
    }
}
