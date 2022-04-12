<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Services;

use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Http\Url;
use Github\Client;

class GitHub
{
    public function __construct(
        protected Client $client = new Client()
    ) {
    }

    /**
     * Get a filtered list of repositories for the selected organization.
     *
     * @param string $organization
     *
     * @return array
     */
    public function repositories(string $organization): array
    {
        return Arr::filter($this->all($organization), fn (array $org): bool => $this->allow($org));
    }

    /**
     * Get the entire list of repositories for the selected organization.
     *
     * @param string $organization
     *
     * @return array
     */
    public function all(string $organization): array
    {
        $repositories = [];

        $page = 1;

        while ($repos = $this->client->organization()->repositories($organization, 'public', $page)) {
            $repositories = array_merge($repositories, $repos);

            $page++;
        }

        return $repositories;
    }

    /**
     * Clone repository for further processing.
     *
     * @param string $ssh_url
     * @param string $directory
     *
     * @return void
     */
    public function download(string $ssh_url, string $directory): void
    {
        exec('git clone ' . $ssh_url . ' ' . $directory);
    }

    protected function allow(array $organization): bool
    {
        return $this->doesntPrivate($organization)
               && $this->doesntDot($organization)
               && $this->doesntHasNoDoc($organization);
    }

    protected function doesntDot(array $organization): bool
    {
        $name = Arr::get($organization, 'name');

        return ! Str::startsWith($name, '.');
    }

    protected function doesntPrivate(array $organization): bool
    {
        return ! Arr::get($organization, 'private');
    }

    protected function doesntHasNoDoc(array $organization): bool
    {
        return ! $this->hasUrl($organization, '.nodoc');
    }

    protected function hasUrl(array $organization, string $filename): bool
    {
        $name = Arr::get($organization, 'full_name');

        $url = sprintf('https://raw.githubusercontent.com/%s/main/%s', $name, $filename);

        return Url::exists($url);
    }
}
