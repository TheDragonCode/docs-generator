<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Dto;

use DragonCode\SimpleDataTransferObject\DataTransferObject;
use DragonCode\Support\Facades\Helpers\Str;

class Preview extends DataTransferObject
{
    public ?string $brand = null;

    public ?string $vendor = null;

    public ?string $name = null;

    protected function castBrand(?string $value): ?string
    {
        return $value ? Str::lower($value) : null;
    }

    protected function castVendor(string $value): string
    {
        return Str::slug($value);
    }

    protected function castName(string $value): string
    {
        return Str::slug($value);
    }
}
