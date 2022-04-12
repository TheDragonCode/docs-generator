<?php

namespace DragonCode\DocsGenerator\Templates;

use DragonCode\Contracts\Support\Stringable;
use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;

/**
 * @method static Template make(string $method, string $summary, ?string $description = null)
 */
abstract class Template implements Stringable
{
    use Makeable;

    protected int $header_level = 0;

    public function __construct(
        protected string  $header,
        protected string  $summary,
        protected ?string $description = null
    ) {
    }

    public function __toString(): string
    {
        return Arr::of([
            $this->getMethodName(),
            $this->getSummary(),
            $this->getDescription(),
        ])
            ->filter()
            ->implode(PHP_EOL . PHP_EOL);
    }

    protected function getMethodName(): string
    {
        return Str::of($this->header)
            ->squish()
            ->prepend(' ')
            ->prepend(str_pad('', $this->header_level, '#'))
            ->trim();
    }

    protected function getSummary(): string
    {
        return Str::of($this->summary)
            ->squish()
            ->trim();
    }

    protected function getDescription(): ?string
    {
        return Str::of($this->description)
            ->squish()
            ->trim();
    }
}
