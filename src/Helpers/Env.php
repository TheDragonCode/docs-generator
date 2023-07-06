<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Helpers;

use Dotenv\Repository\AdapterRepository;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use DragonCode\Support\Concerns\Resolvable;
use PhpOption\Option;

class Env
{
    use Resolvable;

    /**
     * Get the value of environment settings.
     *
     * return Env::get('GITHUB_TOKEN')
     * // 02d95b05-0515-4480-91bf-37a7c86e2274
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return self::resolveCallback($key, fn (string $key) => $this->getValue($key, $default));
    }

    protected function getValue(string $key, mixed $default = null): mixed
    {
        $value = $this->repository()->get($key);

        return Option::fromValue($value)->getOrElse($default);
    }

    protected function repository(): AdapterRepository|RepositoryInterface
    {
        return self::resolveInstance(RepositoryBuilder::createWithDefaultAdapters()->immutable()->make());
    }
}
