<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Helpers;

use DragonCode\DocsGenerator\Dto\Preview;
use DragonCode\Support\Concerns\Resolvable;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;

class Composer
{
    use Resolvable;

    /**
     * Returns a list of namespaces defined in the composer.json file.
     *
     * [
     *     'DragonCode\DocsGenerator\' => 'src'
     * ]
     *
     * @param string $directory
     *
     * @return array
     */
    public function namespaces(string $directory): array
    {
        $names = $this->config($directory, 'autoload.psr-4', []);

        return Arr::ksort($names);
    }

    /**
     * Returns a list of ignored namespaces.
     *
     * [
     *     'DragonCode\DocsGenerator\Console',
     *     'DragonCode\DocsGenerator\Dto',
     *     'DragonCode\DocsGenerator\Enum',
     * ]
     *
     * @param string $directory
     *
     * @return array
     */
    public function ignoreNamespaces(string $directory): array
    {
        return $this->config($directory, 'extra.dragon-code.docs-generator.ignore', []);
    }

    /**
     * Gets the package description.
     *
     * Document generation assistant.
     *
     * @param string $directory
     *
     * @return string
     */
    public function description(string $directory): string
    {
        return $this->config($directory, 'description');
    }

    /**
     * Gets the package full name from the composer.json file.
     *
     * dragon-code/docs-generator
     *
     * @param string $directory
     *
     * @return string
     */
    public function fullName(string $directory): string
    {
        return $this->config($directory, 'name');
    }

    /**
     * Gets the name of the vendor.
     *
     * The Dragon Code
     *
     * @param string $directory
     *
     * @return string
     */
    public function vendor(string $directory): string
    {
        $name = $this->config($directory, 'extra.dragon-code.docs-generator.preview.vendor')
                ?? Str::of($this->fullName($directory))->after('/');

        return Str::of($name)->slug(' ')->title()->toString();
    }

    /**
     * Gets the name of the application.
     *
     * Docs Generator
     *
     * @param string $directory
     *
     * @return string
     */
    public function package(string $directory): string
    {
        return Str::of($this->config($directory, 'name'))->after('/')->slug(' ')->title()->toString();
    }

    public function preview(string $directory): Preview
    {
        $brand  = $this->config($directory, 'extra.dragon-code.docs-generator.preview.brand');
        $vendor = $this->vendor($directory);
        $name   = $this->package($directory);

        return Preview::make(compact('brand', 'vendor', 'name'));
    }

    protected function config(string $directory, string $key, mixed $default = null): mixed
    {
        return self::resolveCallback($key, function (string $key) use ($directory, $default): mixed {
            $composer = File::load(rtrim($directory, '\\/') . '/composer.json');

            return Arr::get($composer, $key, $default);
        });
    }
}