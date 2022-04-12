<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Enum;

enum Stubs: string
{
    case BLOCK = 'block.stub';

    case CODE = 'code.stub';

    case EXAMPLE = 'example.stub';

    case PAGE = 'page.stub';
}
