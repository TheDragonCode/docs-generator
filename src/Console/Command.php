<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Enum\Option;
use DragonCode\DocsGenerator\Services\Package;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Helpers\Boolean;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand
{
    protected string $signature;

    protected string $description;

    protected InputInterface $input;

    protected OutputInterface $output;

    protected string $tmp_path = './temp/source';

    protected string $tmp_docs = './temp/docs';

    abstract protected function handle(): void;

    protected function configure()
    {
        return $this
            ->setName($this->signature)
            ->setDescription($this->description);
    }

    protected function prepare(string $path): void
    {
        if ($this->hasCleanupDocsOption() && ! $this->hasCleanupDocs()) {
            return;
        }

        $this->line(Message::CLEANUP());

        Directory::ensureDelete($path);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        $this->handle();

        return 0;
    }

    #[Pure]
    protected function package(): Package
    {
        return new Package($this->basePath());
    }

    protected function line(string $value, ?string $style = null): void
    {
        $this->output->isDecorated() && ! empty($style)
            ? $this->output->writeln("<$style>$value</$style>")
            : $this->output->writeln($value);
    }

    protected function info(string $value): void
    {
        $this->line($value, 'info');
    }

    protected function error(string $value): void
    {
        $this->line($value, 'error');
    }

    protected function getSourcePath(string $name): string
    {
        return $this->tmp_path . '/' . $name;
    }

    protected function getDocsPath(string $name): string
    {
        return $this->tmp_docs . '/' . $name;
    }

    protected function basePath(): string
    {
        return $this->hasBasePath() ? $this->getOptionValue(Option::PATH) : '.';
    }

    protected function hasBasePath(): bool
    {
        return $this->input->hasOption(Option::PATH());
    }

    protected function docsPath(): string
    {
        return $this->getOptionValue(Option::DOCS_PATH, false);
    }

    protected function hasCleanupDocs(): bool
    {
        $value = $this->getOptionValue(Option::CLEANUP_DOCS, false);

        return Boolean::parse($value) ?? true;
    }

    protected function getOptionValue(Option $option, bool $use_real = true): bool|string
    {
        $value = $this->input->getOption($option->value);

        return $use_real ? realpath($value) : $value;
    }

    protected function hasCleanupDocsOption(): bool
    {
        return $this->input->hasOption(Option::CLEANUP_DOCS());
    }
}
