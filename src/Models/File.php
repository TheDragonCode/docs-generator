<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Models;

use DragonCode\Support\Facades\Helpers\Str;

class File
{
    protected string $extension = '.md';

    public function __construct(
        protected string $prefix,
        protected string $namespace
    ) {
    }

    public function getNamespace(): string
    {
        return Str::of($this->namespace)->start('\\')->toString();
    }

    public function getMarkdownFilename(): string
    {
        return Str::of($this->getNamespace())
            ->trim('\\')
            ->after($this->prefix)
            ->explode('\\')
            ->map(static fn (string $value) => Str::of($value)->snake()->slug())
            ->implode('/')
            ->append($this->extension)
            ->toString();
    }
}
