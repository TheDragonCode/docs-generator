<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Enum;

use ArchTech\Enums\InvokableCases;
use JetBrains\PhpStorm\Pure;

/**
 * @method static string PREPARE_GENERATE()
 * @method static string RECEIVING_REPOSITORIES()
 */
enum Message: string
{
    use InvokableCases;

    case PREPARE_GENERATE = 'Prepare docs generating...';

    case RECEIVING_REPOSITORIES = 'Receiving repositories list...';

    case PROCESSING_CLASS = 'Processing %s...';

    case DOWNLOADING = 'Downloading %s...';

    #[Pure]
    public static function PROCESSING(string $class): string
    {
        return self::sprint(self::PROCESSING_CLASS, $class);
    }

    public static function DOWNLOADING(string $name): string
    {
        return self::sprint(self::DOWNLOADING, $name);
    }

    protected static function sprint(Message $message, string $value): string
    {
        return sprintf($message->value, $value);
    }
}
