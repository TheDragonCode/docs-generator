<?php

declare(strict_types=1);

namespace Tests;

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
}
