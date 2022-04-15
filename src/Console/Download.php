<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Console;

use DragonCode\DocsGenerator\Enum\Message;
use DragonCode\DocsGenerator\Facades\GitHub;
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

            $this->download($url, $name);
        }
    }

    protected function download(string $url, string $name): void
    {
        $path = $this->tmp_path . '/' . $name;

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
}
