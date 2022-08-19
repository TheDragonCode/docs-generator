<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Enum;

use ArchTech\Enums\InvokableCases;
use JetBrains\PhpStorm\Pure;

/**
 * @method static string CLEANUP()
 * @method static string PREPARE_GENERATE()
 * @method static string RECEIVING_REPOSITORIES()
 */
enum Message: string
{
    use InvokableCases;

    case CLEANUP = 'Pre-cleaning...';

    case RECEIVING_REPOSITORIES = 'Receiving repositories list...';

    case PROCESSING_CLASS = 'Processing %s...';

    case DOWNLOADING = 'Downloading %s...';

    case INSTALLING = 'Installing %s...';

    case GENERATING = 'Generating %s...';

    #[Pure]
    public static function PROCESSING(string $class): string
    {
        return self::sprint(self::PROCESSING_CLASS, $class);
    }

    #[Pure]
    public static function DOWNLOADING(string $name): string
    {
        return self::sprint(self::DOWNLOADING, $name);
    }

    #[Pure]
    public static function INSTALLING(string $name): string
    {
        return self::sprint(self::INSTALLING, $name);
    }

    #[Pure]
    public static function GENERATING(string $name): string
    {
        return self::sprint(self::GENERATING, $name);
    }

    protected static function sprint(Message $message, string $value): string
    {
        return sprintf($message->value, $value);
    }
}
