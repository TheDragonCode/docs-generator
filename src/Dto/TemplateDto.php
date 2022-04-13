<?php

namespace DragonCode\DocsGenerator\Dto;

use DragonCode\SimpleDataTransferObject\DataTransferObject;
use DragonCode\Support\Facades\Helpers\Arr;

class TemplateDto extends DataTransferObject
{
    public ?string $class = null;

    public ?string $class_short = null;

    public ?string $method = null;

    public ?string $summary = null;

    public ?string $example = null;

    public ?string $content = null;

    public ?string $code = null;

    public function toArray(): array
    {
        return Arr::renameKeys(parent::toArray(), fn (string $key) => sprintf('{{%s}}', $key));
    }

    protected function castClass(string $value): string
    {
        $this->class_short = Arr::last(explode('\\', $value));

        return $value;
    }
}
