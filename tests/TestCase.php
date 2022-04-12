<?php

declare(strict_types=1);

namespace Tests;

use DragonCode\Support\Facades\Application\OS;
use DragonCode\Support\Facades\Filesystem\Directory;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected string $docs_path = __DIR__ . '/docs';

    protected function setUp(): void
    {
        parent::setUp();

        Directory::ensureDelete($this->docs_path);
    }

    protected function exec(string $command): array
    {
        $sudo = OS::isUnix() ? 'sudo' : '';

        exec($sudo . ' ' . $command, $output);

        return $output;
    }
}
