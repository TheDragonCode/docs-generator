<?php

declare(strict_types=1);

namespace Tests;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Facades\Env;
use DragonCode\DocsGenerator\Facades\GitHub;
use DragonCode\DocsGenerator\Processors\PageProcessor;
use DragonCode\DocsGenerator\Services;

class GenerateTest extends TestCase
{
    public function testGenerate()
    {
        $this->assertDirectoryDoesNotExist($this->docs_path);

        $bin = realpath(__DIR__ . '/../bin/docs');

        $output = $this->exec('php ' . $bin . ' generate --docs-dir=' . $this->docs_path);

        $this->assertSame([
            Message::PREPARE_GENERATE(),
            Message::PROCESSING(Env::class),
            Message::PROCESSING(GitHub::class),
            Message::PROCESSING(PageProcessor::class),
            Message::PROCESSING(Services\GitHub::class),
            Message::PROCESSING(Services\Package::class),
        ], $output);

        $this->assertDirectoryExists($this->docs_path);
    }
}
