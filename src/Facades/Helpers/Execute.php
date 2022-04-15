<?php

namespace DragonCode\DocsGenerator\Facades\Helpers;

use DragonCode\DocsGenerator\Helpers\Execute as Helper;
use DragonCode\Support\Facades\Facade;

/**
 * @method static array call(string $command, array $options = [])
 */
class Execute extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Helper::class;
    }
}
