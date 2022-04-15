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

    protected string $description = 'Download repositories and generate docs';

    protected function configure()
    {
        parent::configure()->addArgument('organization');
    }

    protected function handle(): void
    {
        $this->prepare($this->tmp_path);
        $this->process();
    }

    protected function process(): void
    {
        foreach ($this->getRepositories() as $repository) {
            $url  = Arr::get($repository, 'ssh_url');
            $name = Arr::get($repository, 'name');

            $this->line(Message::PROCESSING($name));

            $path = $this->getSourcePath($name);

            $this->download($name, $url, $path);
            $this->install($name, $path);
            $this->generate($name, $path);
        }
    }

    protected function download(string $name, string $url, string $path): void
    {
        $this->info(Message::DOWNLOADING($name));

        Directory::ensureDirectory($path, can_delete: true);

        GitHub::download($url, $path);
    }

    protected function install(string $name, string $path): void
    {
        $this->info(Message::INSTALLING($name));

        $package = $this->package()->fullName();

        Execute::call('composer require ' . $package . ':dev-main', [
            'working-dir'    => $path,
            'no-interaction' => null,
            'no-progress'    => null,
            'no-plugins'     => null,
            'quiet'          => null,
        ]);
    }

    protected function generate(string $name, string $path): void
    {
        $this->info(Message::GENERATING($name));

        Execute::call('php ' . $path . '/vendor/bin/docs generate', [
            Option::DOCS_PATH()    => $this->tmp_docs,
            Option::CLEANUP_DOCS() => 'false',
        ]);
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
}
