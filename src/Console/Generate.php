<?php

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Enum\Option;
use DragonCode\DocsGenerator\Models\File as FileDTO;
use DragonCode\DocsGenerator\Processors\IndexProcessor;
use DragonCode\DocsGenerator\Processors\PageProcessor;
use DragonCode\DocsGenerator\Processors\Processor;
use DragonCode\DocsGenerator\Services\Package;
use DragonCode\Support\Facades\Filesystem\File;
use Symfony\Component\Console\Input\InputOption;

class Generate extends Command
{
    protected string $signature = 'generate';

    protected string $description = 'Document generation';

    protected function configure()
    {
        return parent::configure()
            ->addOption(
                Option::PATH(),
                mode       : InputOption::VALUE_OPTIONAL,
                description: 'Specifies a different path for search code',
                default    : '.'
            )
            ->addOption(
                Option::DOCS_PATH(),
                mode       : InputOption::VALUE_OPTIONAL,
                description: 'Specifies a different path for generating documentation',
                default    : './docs'
            )
            ->addOption(
                Option::CLEANUP_DOCS(),
                mode       : InputOption::VALUE_OPTIONAL,
                description: 'Specifies whether to delete the documentation folder before generating',
                default    : 'true'
            );
    }

    protected function handle(): void
    {
        $this->prepare($this->docsPath());

        $package = $this->package();

        $this->main($package);
        $this->pages($package);
    }

    protected function main(Package $package): void
    {
        $this->process(IndexProcessor::class, $package, 'index.md', Message::PROCESSING('main'));
    }

    protected function pages(Package $package): void
    {
        dd($package->files());
        foreach ($package->files() as $file) {
            $this->process(PageProcessor::class, $package, $file, Message::PROCESSING($file->getShowNamespace()));
        }
    }

    protected function process(string $processor, Package $package, FileDTO|string $file, string $message): void
    {
        $this->line($message);

        $path = $this->targetPath($file);

        $content = $this->getContent($processor, $package, $file);

        $this->store($path, $content);
    }

    protected function getContent(Processor|string $processor, Package $package, FileDTO|string $file): string
    {
        return $processor::make($package, $file)->get();
    }

    protected function targetPath(FileDTO|string $path): string
    {
        $path = is_object($path) ? $path->getMarkdownFilename() : $path;

        return rtrim($this->docsPath(), '\\/') . DIRECTORY_SEPARATOR . $path;
    }

    protected function store(string $path, string $content): void
    {
        File::store($path, $content);
    }
}
