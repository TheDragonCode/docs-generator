<?php

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Models\File as FileDTO;
use DragonCode\DocsGenerator\Processors\IndexProcessor;
use DragonCode\DocsGenerator\Processors\PageProcessor;
use DragonCode\DocsGenerator\Processors\Processor;
use DragonCode\DocsGenerator\Services\Package;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Input\InputOption;

class Generate extends Command
{
    protected string $signature = 'generate';

    protected string $description = 'Document generation';

    protected string $base_path = '.';

    protected function configure()
    {
        return parent::configure()->addOption(
            'docs-dir',
            mode       : InputOption::VALUE_OPTIONAL,
            description: 'Specifies a different path for generating documentation',
            default    : './docs'
        );
    }

    protected function handle(): void
    {
        $this->prepare();

        $package = $this->package();

        $this->main($package);
        //$this->pages($package);
    }

    protected function prepare(): void
    {
        $this->output->writeln(Message::PREPARE_GENERATE());

        Directory::ensureDelete($this->docsPath());
    }

    protected function main(Package $package): void
    {
        $this->process(IndexProcessor::class, $package, 'index.md', Message::PROCESSING('main'));
    }

    protected function pages(Package $package): void
    {
        foreach ($package->files() as $file) {
            $this->process(PageProcessor::class, $package, $file, Message::PROCESSING($file));
        }
    }

    protected function process(string $processor, Package $package, FileDTO|string $file, string $message): void
    {
        $this->output->writeln($message);

        $path = $this->targetPath($file);

        $content = $this->getContent($processor, $package, $file);

        $this->store($path, $content);
    }

    protected function getContent(Processor|string $processor, Package $package, FileDTO|string $file): string
    {
        return $processor::make($package, $file)->get();
    }

    #[Pure]
    protected function package(): Package
    {
        return new Package($this->basePath());
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

    protected function basePath(): string
    {
        return realpath($this->base_path);
    }

    protected function docsPath(): string
    {
        return $this->input->getOption('docs-dir');
    }
}
