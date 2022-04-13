<?php

namespace DragonCode\DocsGenerator\Dto;

use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Str;

class FileInfo
{
    public function __construct(
        protected string $base_path,
        protected string $filename
    ) {
    }

    public function dirname(): string
    {
        return Path::dirname($this->filename);
    }

    public function filename(): string
    {
        return Path::filename($this->filename);
    }

    public function basename(): string
    {
        return Str::before($this->filename, '.php');
    }

    public function markdown(): string
    {
        return Str::of($this->dirname())
            ->replace('\\/', DIRECTORY_SEPARATOR)
            ->append(DIRECTORY_SEPARATOR)
            ->append($this->filename())
            ->explode(DIRECTORY_SEPARATOR)
            ->map(fn (string $value) => Str::slug($value))
            ->implode(DIRECTORY_SEPARATOR)
            ->append('.md');
    }

    public function source(): string
    {
        return Str::of(realpath($this->base_path))
            ->trim('\\/')
            ->prepend('/')
            ->append('/')
            ->append($this->filename);
    }
}
