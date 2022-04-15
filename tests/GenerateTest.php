<?php

declare(strict_types=1);

namespace Tests;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Enum\Option;
use DragonCode\DocsGenerator\Facades;
use DragonCode\DocsGenerator\Helpers;
use DragonCode\DocsGenerator\Models;
use DragonCode\DocsGenerator\Services;

class GenerateTest extends TestCase
{
    public function testGenerate()
    {
        $this->assertDirectoryDoesNotExist($this->docs_path);

        $bin = realpath(__DIR__ . '/../bin/docs');

        $output = $this->exec('php ' . $bin . ' generate', [
            Option::PATH()      => $this->path,
            Option::DOCS_PATH() => $this->docs_path,
        ]);

        $this->assertSame([
            Message::PREPARE_GENERATE(),
            Message::PROCESSING('main'),
            Message::PROCESSING(Facades\Env::class),
            Message::PROCESSING(Facades\GitHub::class),
            Message::PROCESSING(Facades\Services\Finder::class),
            Message::PROCESSING(Helpers\Composer::class),
            Message::PROCESSING(Helpers\Env::class),
            Message::PROCESSING(Models\File::class),
            Message::PROCESSING(Services\Finder::class),
            Message::PROCESSING(Services\GitHub::class),
            Message::PROCESSING(Services\Package::class),
        ], $output);

        $this->assertDirectoryExists($this->docs_path);
    }
}
