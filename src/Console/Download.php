<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Enum\Option;
use DragonCode\DocsGenerator\Facades\GitHub;
use DragonCode\DocsGenerator\Facades\Helpers\Execute;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Helpers\Arr;

class Download extends Command
{
    protected string $signature = 'download';

    protected string $description = 'Download repositories';

    protected function configure()
    {
        parent::configure()->addArgument('organization');
    }

    protected function handle(): void
    {
        $this->prepare();
        $this->process();
    }

    protected function prepare(): void
    {
        Directory::ensureDelete($this->tmp_path);
    }

    protected function process(): void
    {
        foreach ($this->getRepositories() as $repository) {
            $url  = Arr::get($repository, 'ssh_url');
            $name = Arr::get($repository, 'name');

            $this->line(Message::DOWNLOADING($name));

            $path = $this->getSourcePath($name);

            $this->download($url, $path);
            $this->generate($path, $name);
        }
    }

    protected function generate(string $path, string $name): void
    {
        $bin = __DIR__ . '/../../bin/docs';

        Execute::call('php ' . $bin . ' generate', [
            Option::PATH()         => $path,
            Option::DOCS_PATH()    => $this->tmp_docs . '/' . $name,
            Option::CLEANUP_DOCS() => false,
        ]);
    }

    protected function download(string $url, string $path): void
    {
        Directory::ensureDirectory($path, can_delete: true);

        GitHub::download($url, $path);
    }

    protected function getRepositories(): array
    {
        $this->line(Message::RECEIVING_REPOSITORIES());

        return GitHub::repositories($this->getOrganization());
    }

    protected function getOrganization(): string
    {
        if ($name = $this->input->getArgument('organization')) {
            return $name;
        }

        $this->error('<error>You did not enter an organization name</error>');

        exit(1);
    }

    protected function getSourcePath(string $name): string
    {
        return $this->tmp_path . '/' . $name;
    }
}
