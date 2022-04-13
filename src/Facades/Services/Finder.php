<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Facades\Services;

use DragonCode\DocsGenerator\Services\Finder as Service;
use DragonCode\Support\Facades\Facade;

/**
 * @method static array files(string $path)
 */
class Finder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}
