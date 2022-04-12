<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Services;

use DragonCode\Support\Facades\Facade;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Instance;
use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Package
{
    public function __construct(
        protected string $path
    ) {
    }

    public function files(): array
    {
        $files = [];

        foreach ($this->getNamespaces() as $namespace => $directory) {
            $names = File::names($this->getAppPath($directory),
                static fn (string $file) => Str::endsWith($file, '.php'),
                recursive: true
            );

            $names = Arr::of($names)
                ->unique()
                ->flip()
                ->map(static fn (string $class, string $file) => Str::of($file)
                    ->before('.php')
                    ->replace('/', '\\')
                    ->prepend($namespace)
                    ->toString()
                )
                ->filter(fn ($file) => $this->allowFile($file))
                ->toArray();

            $files = array_merge($files, $names);
        }

        return $files;
    }

    protected function allowFile(string $class): bool
    {
        return Instance::of($class, [Facade::class, IlluminateFacade::class]);
    }

    protected function getNamespaces(): array
    {
        $composer = File::load($this->getAppPath('composer.json'));

        return Arr::get($composer, 'autoload.psr-4', []);
    }

    protected function getAppPath(string $filename): string
    {
        return implode(DIRECTORY_SEPARATOR, [$this->path, $filename]);
    }
}
