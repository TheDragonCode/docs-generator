<?php

declare(strict_types=1);

namespace DragonCode\DocsGenerator\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand
{
    protected string $signature;

    protected string $description;

    protected InputInterface $input;

    protected OutputInterface $output;

    protected string $tmp_path = './temp';

    abstract protected function handle(): void;

    protected function configure()
    {
        return $this
            ->setName($this->signature)
            ->setDescription($this->description);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        $this->handle();

        return 0;
    }
}
