<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Enum;

enum Stubs: string
{
    case INSTALL_STUB = 'install.stub';

    case CLASS_STUB = 'class.stub';

    case CODE_DYNAMIC_STUB = 'code-dynamic.stub';

    case CODE_STATIC_STUB = 'code-static.stub';

    case EXAMPLE_STUB = 'example.stub';

    case LIST_ITEM_STUB = 'list-item.stub';

    case MAIN_STUB = 'main.stub';

    case METHOD_STUB = 'method.stub';
}
