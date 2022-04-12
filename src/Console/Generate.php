<?php

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Dto\FileInfo;
use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Processors\Helper;
use DragonCode\DocsGenerator\Services\Package;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Input\InputOption;

class Generate extends Command
{
    protected string $signature = 'generate';

    protected string $description = 'Document generation';

    protected string $base_path = '.';

    protected string $docs_path = './docs';

    protected function configure()
    {
        return parent::configure()->addOption('docs-dir',
            mode       : InputOption::VALUE_OPTIONAL,
            description: 'Specifies a different path for generating documentation'
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

            $path = $this->targetPath($info->dirname(), $info->filename());

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

    protected function targetPath(string $directory, string $filename): string
    {
        return Str::of($this->docsPath())
            ->end(DIRECTORY_SEPARATOR)
            ->append(Str::lower($directory))
            ->append(DIRECTORY_SEPARATOR)
            ->append(Str::lower($filename))
            ->append('.md');
    }

    protected function basePath(): string
    {
        return realpath($this->base_path);
    }

    protected function docsPath(): string
    {
        return $this->input->getOption('docs-dir') ?: $this->docs_path;
    }
}
