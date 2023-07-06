<?php

namespace DragonCode\DocsGenerator\Helpers;

use DragonCode\Support\Facades\Helpers\Arr;

class Execute
{
    /**
     * Execute an external program.
     */
    public function call(string $command, array $options = []): array
    {
        exec($command . ' ' . $this->compileOptions($options), $output);

        return $output;
    }

    protected function compileOptions(array $options): string
    {
        return Arr::of($options)
            ->map(fn (?string $value, string $key) => $this->compileOption($key, $value))
            ->implode(' ')
            ->toString();
    }

    protected function compileOption(string $key, ?string $value): string
    {
        return is_null($value) ? '--' . $key : sprintf('--%s=%s', $key, $value);
    }
}
