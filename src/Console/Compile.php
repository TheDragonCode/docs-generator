<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Enum\Option;
use DragonCode\DocsGenerator\Facades\Helpers\Execute;
use DragonCode\Support\Facades\Filesystem\Directory;

class Compile extends Command
{
    protected string $signature = 'compile';

    protected string $description = 'Generation of package documentation and preparation for deployment';

    protected function handle(): void
    {
        $this->prepare($this->tmp_docs);
        $this->process();
    }

    protected function process(): void
    {
        foreach ($this->projects() as $project) {
            $this->line(Message::PROCESSING($project));

            $source = $this->getSourcePath($project);
            $docs   = $this->getDocsPath($project);

            $this->generate($source, $docs);
        }
    }

    protected function generate(string $source_path, string $docs_path): void
    {
        $bin = __DIR__ . '/../../bin/docs';

        Execute::call('php ' . $bin . ' generate', [
            Option::PATH()         => $source_path,
            Option::DOCS_PATH()    => $docs_path,
            Option::CLEANUP_DOCS() => false,
        ]);
    }

    protected function projects(): array
    {
        return Directory::names($this->tmp_path);
    }
}
