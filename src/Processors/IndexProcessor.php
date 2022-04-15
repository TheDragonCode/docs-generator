<?php

namespace DragonCode\DocsGenerator\Processors;

use DragonCode\DocsGenerator\Dto\Template;
use DragonCode\DocsGenerator\Enum\Stubs;

class IndexProcessor extends Processor
{
    public function get(): string
    {
        $package = $this->package->package();
        $vendor  = $this->package->vendor();
        $summary = $this->package->description();

        $bash = $this->install();

        $content = $this->collect();

        return $this->stub(Stubs::MAIN_STUB, new Template(compact('package', 'vendor', 'summary', 'bash', 'content')));
    }

    protected function collect(): string
    {
        $result = [];

        foreach ($this->package->files() as $file) {
            $title = $file->getShowNamespace();

            $link = $file->getMarkdownFilename();

            $result[] = $this->stub(Stubs::LIST_ITEM_STUB, new Template(compact('title', 'link')));
        }

        return implode('', $result);
    }

    protected function install(): string
    {
        $title   = $this->package->package();
        $package = $this->package->fullName();

        return $this->stub(Stubs::INSTALL_STUB, new Template(compact('title', 'package')));
    }
}
