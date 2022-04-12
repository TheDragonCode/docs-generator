<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Facades;

use DragonCode\DocsGenerator\Helpers\GitHub as Helper;
use DragonCode\Support\Facades\Facade;

/**
 * @method static array all(string $organization)
 * @method static array download(string $ssh_url, string $directory)
 * @method static array repositories(string $organization)
 */
class GitHub extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Helper::class;
    }
}
