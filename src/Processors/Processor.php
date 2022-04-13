<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\Template;
use DragonCode\DocsGenerator\Enum\Stubs;
use DragonCode\DocsGenerator\Models\File;
use DragonCode\DocsGenerator\Models\File as FileDTO;
use DragonCode\DocsGenerator\Services\Package;
use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Facade;
use DragonCode\Support\Facades\Instances\Instance;
use DragonCode\Support\Facades\Tools\Stub;
use phpDocumentor\Reflection\DocBlockFactory;

/**
 * @method static Processor make(Package $package, File|string $file)
 */
abstract class Processor
{
    use Makeable;

    protected DocBlockFactory $doc;

    public function __construct(
        protected Package        $package,
        protected FileDTO|string $file
    ) {
        $this->doc = DocBlockFactory::createInstance();
    }

    abstract public function get(): string;

    protected function stub(Stubs $stub, Template $template): string
    {
        return Stub::replace(__DIR__ . '/../../stubs/' . $stub->value, $template->toArray());
    }

    protected function isFacade(Facade|string $namespace): bool
    {
        return Instance::of($namespace, [Facade::class, '\Illuminate\Support\Facades\Facade']);
    }
}
