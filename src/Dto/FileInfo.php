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

    public function source(): string
    {
        return Str::of(realpath($this->base_path))
            ->trim('\\/')
            ->append('/')
            ->append($this->filename);
    }
}
