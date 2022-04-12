<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Facades;

use DragonCode\DocsGenerator\Helpers\Env as Helper;
use DragonCode\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 */
class Env extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Helper::class;
    }
}
