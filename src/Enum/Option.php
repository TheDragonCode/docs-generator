<?php

namespace DragonCode\DocsGenerator\Enum;

use ArchTech\Enums\InvokableCases;

/**
 * @method static string PATH()
 * @method static string DOCS_PATH()
 */
enum Option: string
{
    use InvokableCases;

    case PATH = 'path';

    case DOCS_PATH = 'docs-path';
}
