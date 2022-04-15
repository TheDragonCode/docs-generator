<?php

namespace DragonCode\DocsGenerator\Helpers;

use DragonCode\Support\Facades\Helpers\Arr;

class Execute
{
    /**
     * Execute an external program.
     *
     * @param string $command
     * @param array $options
     *
     * @return array
     */
    public function call(string $command, array $options = []): array
    {
        exec($command . ' ' . $this->compileOptions($options), $output);

        return $output;
    }

    protected function compileOptions(array $options): string
    {
        return Arr::of($options)
            ->map(static fn (string $value, string $key) => sprintf('--%s=%s', $key, $value))
            ->implode(' ')
            ->toString();
    }
}
