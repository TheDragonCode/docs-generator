<?php

namespace DragonCode\DocsGenerator\Dto;

use DragonCode\SimpleDataTransferObject\DataTransferObject;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;

class Template extends DataTransferObject
{
    public ?string $class = null;

    public ?string $class_short = null;

    public ?string $method = null;

    public ?string $summary = null;

    public ?string $example = null;

    public ?string $content = null;

    public ?string $code = null;

    public ?string $vendor = null;

    public ?string $vendor_slug = null;

    public ?string $package = null;

    public ?string $package_slug = null;

    public ?string $bash = null;

    public ?string $title = null;

    public ?string $link = null;

    public function toArray(): array
    {
        return Arr::renameKeys(parent::toArray(), fn (string $key) => sprintf('{{%s}}', $key));
    }

    protected function castClass(string $value): string
    {
        $this->class_short = Arr::last(explode('\\', $value));

        return $value;
    }

    protected function castVendor(string $value): string
    {
        $this->vendor_slug = $this->slug($value);

        return $value;
    }

    protected function castPackage(string $value): string
    {
        $this->package_slug = $this->slug($value);

        return $value;
    }

    protected function slug(string $value): string
    {
        return Str::of($value)->snake()->slug();
    }
}
