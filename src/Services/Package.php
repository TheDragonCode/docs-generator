<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Services;

use DragonCode\Support\Concerns\Resolvable;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Reflection;

class Package
{
    use Resolvable;

    public function __construct(
        protected string $path
    ) {
    }

    public function files(): array
    {
        return self::resolveCallback('package-files', function () {
            $files = [];

            foreach ($this->getNamespaces() as $namespace => $directory) {
                $names = File::names(
                    $this->getAppPath($directory),
                    static fn (string $file) => Str::endsWith($file, '.php'),
                    recursive: true
                );

                $names = Arr::of($names)
                    ->unique()
                    ->flip()
                    ->map(
                        static fn (string $class, string $file) => Str::of($file)
                            ->before('.php')
                            ->replace('/', '\\')
                            ->prepend($namespace)
                            ->toString()
                    )
                    ->filter(fn ($file) => $this->allow($file, $namespace))
                    ->toArray();

                $files = array_merge($files, $names);
            }

            return $files;
        });
    }

    public function previewBrand(): ?string
    {
        return $this->composer('extra.dragon-code.docs-generator.preview.brand');
    }

    public function previewVendor(): string
    {
        return $this->composer('extra.dragon-code.docs-generator.preview.vendor');
    }

    protected function allow(string $class, string $namespace): bool
    {
        return $this->allowClass($class) && $this->doesntExcept($class, $namespace);
    }

    protected function allowClass(string $class): bool
    {
        $reflect = Reflection::resolve($class);

        return class_exists($class)
               && ! $reflect->isAbstract()
               && ! $reflect->isAnonymous()
               && ! $reflect->isInterface()
               && ! $reflect->isTrait();
    }

    protected function doesntExcept(string $class, string $namespace): bool
    {
        $name = Str::after($class, $namespace);

        return ! Str::startsWith($name, $this->except());
    }

    protected function getNamespaces(): array
    {
        return $this->composer('autoload.psr-4', []);
    }

    protected function except(): array
    {
        return $this->composer('extra.dragon-code.docs-generator.except', []);
    }

    protected function getAppPath(string $filename): string
    {
        return implode(DIRECTORY_SEPARATOR, [$this->path, $filename]);
    }

    protected function composer(string $key, mixed $default = null): mixed
    {
        return self::resolveCallback($key, function (string $key) use ($default) {
            $composer = File::load($this->getAppPath('composer.json'));

            return Arr::get($composer, $key, $default);
        });
    }
}
