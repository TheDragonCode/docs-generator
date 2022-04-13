<?php

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Dto\FileInfo;
use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Processors\Helper;
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

        $this->generate($package);
    }

    protected function prepare(): void
    {
        $this->output->writeln(Message::PREPARE_GENERATE());

        Directory::ensureDelete($this->docsPath());
    }

    protected function generate(Package $package): void
    {
        foreach ($package->files() as $file => $class) {
            $this->output->writeln(Message::PROCESSING($class));

            $info = $this->info($file);

            $path = $this->targetPath($info->markdown());

            $content = $this->getContent($class);

            $this->store($path, $content);
        }
    }

    protected function store(string $path, string $content): void
    {
        File::store($path, $content);
    }

    protected function getContent(string $class): string
    {
        return Helper::make($class)->get();
    }

    #[Pure]
    protected function info(string $filename): FileInfo
    {
        return new FileInfo($this->docsPath(), $filename);
    }

    #[Pure]
    protected function package(): Package
    {
        return new Package($this->basePath());
    }

    protected function targetPath(string $path): string
    {
        return rtrim($this->docsPath(), '\\/') . DIRECTORY_SEPARATOR . $path;
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
