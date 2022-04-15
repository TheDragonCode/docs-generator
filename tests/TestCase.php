<?php

declare(strict_types=1);

namespace Tests;

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Helpers\Arr;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected string $path = __DIR__ . '/../';

    protected string $docs_path = __DIR__ . '/docs';

    protected function setUp(): void
    {
        parent::setUp();

        Directory::ensureDelete($this->docs_path);
    }

    protected function exec(string $command, array $options = []): array
    {
        exec($command . ' ' . $this->compileOptions($options), $output);

        return $output;
    }

    protected function compileOptions(array $options): string
    {
        return Arr::of($options)
            ->map(static fn (string $value, string $key) => sprintf('--%s=%s', $key, $value))
            ->implode(' ')
            ->toString();
    }
}
