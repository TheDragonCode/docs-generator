<?php

namespace DragonCode\DocsGenerator\Enum;

use ArchTech\Enums\InvokableCases;

/**
 * @method static string PATH()
 * @method static string DOCS_PATH()
 * @method static string CLEANUP_DOCS()
 */
enum Option: string
{
    use InvokableCases;

    case PATH         = 'path';
    case DOCS_PATH    = 'docs-path';
    case CLEANUP_DOCS = 'cleanup-docs';
}
