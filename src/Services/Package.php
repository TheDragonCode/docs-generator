<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Services;

use DragonCode\DocsGenerator\Dto\Preview;
use DragonCode\DocsGenerator\Facades\Services\Finder as FinderFacade;
use DragonCode\DocsGenerator\Helpers\Composer;
use DragonCode\Support\Concerns\Resolvable;
use JetBrains\PhpStorm\Pure;

class Package
{
    use Resolvable;

    #[Pure]
    public function __construct(
        protected string $path,
        protected Composer $composer = new Composer()
    ) {}

    /**
     * @return array<\DragonCode\DocsGenerator\Models\File>
     */
    public function files(): array
    {
        return FinderFacade::files($this->path);
    }

    /**
     * Gets the package description.
     *
     * Document generation assistant.
     */
    public function description(): string
    {
        return $this->composer->description($this->path);
    }

    /**
     * Gets the package full name from the composer.json file.
     *
     * dragon-code/docs-generator
     */
    public function fullName(): string
    {
        return $this->composer->fullName($this->path);
    }

    /**
     * Gets the name of the vendor.
     *
     * The Dragon Code
     */
    public function vendor(): string
    {
        return $this->composer->vendor($this->path);
    }

    /**
     * Gets the name of the application.
     *
     * Docs Generator
     */
    public function package(): string
    {
        return $this->composer->package($this->path);
    }

    /**
     * Returns an Preview instance.
     */
    public function preview(): Preview
    {
        return $this->composer->preview($this->path);
    }
}
