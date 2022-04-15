<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Services;

use DragonCode\DocsGenerator\Helpers\Composer;
use DragonCode\DocsGenerator\Models\File as FileModel;
use DragonCode\Support\Concerns\Resolvable;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Facades\Instances\Reflection;
use DragonCode\Support\Helpers\Ables\Arrayable;
use JetBrains\PhpStorm\Pure;

class Finder
{
    use Resolvable;

    #[Pure]
    public function __construct(
        protected Composer $composer = new Composer()
    ) {
    }

    /**
     * Returns a list of files found in the project for generating documentation.
     *
     * [
     *     new Instance(),
     *     new Instance(),
     *     new Instance(),
     * ]
     *
     * @param string $path
     *
     * @return FileModel[]|array
     */
    public function files(string $path): array
    {
        return self::resolveCallback('directory:' . $path, function () use ($path): array {
            $files = [];

            foreach ($this->getNamespaces($path) as $namespace => $directory) {
                $names = $this->find(rtrim($path, '\\/') . '/' . ltrim($directory, '\\/'), $namespace);

                dump(count($names->toArray()));
                $files = array_merge($files, $this->filter($names, $path));
                dd(count($files));
            }

            return $files;
        });
    }

    protected function filter(Arrayable $files, string $path): array
    {
        return $files
            ->filter(fn (FileModel $file) => $this->allow($file->getNamespace(), $path))
            ->toArray();
    }

    protected function allow(string $class, string $path): bool
    {
        return $this->allowNamespace($class, $path)
               && $this->allowClassType($class);
    }

    protected function allowNamespace(string $class, string $path): bool
    {
        $ignore = $this->getIgnoreNamespaces($path);

        return ! Str::startsWith(ltrim($class, '\\'), $ignore);
    }

    protected function allowClassType(string $class): bool
    {
        dump('  ' . (int) class_exists($class) . ' : ' . $class);
        if (! class_exists($class)) {
            return false;
        }

        $reflect = Reflection::resolve($class);

        return ! $reflect->isAbstract()
               && ! $reflect->isAnonymous()
               && ! $reflect->isInterface()
               && ! $reflect->isTrait();
    }

    protected function find(string $path, string $namespace): Arrayable
    {
        $files = File::names($path, static fn (string $file) => Path::extension($file) === 'php', true);

        return Arr::of($files)
            ->sort()
            ->map(
                static fn (string $file) => Str::of($file)
                    ->prepend($namespace)
                    ->before('.php')
                    ->replace('/', '\\')
                    ->toString()
            )->map(fn ($file) => new FileModel($namespace, $file));
    }

    protected function getNamespaces(string $directory): array
    {
        return $this->composer->namespaces($directory);
    }

    protected function getIgnoreNamespaces(string $directory): array
    {
        return $this->composer->ignoreNamespaces($directory);
    }
}
