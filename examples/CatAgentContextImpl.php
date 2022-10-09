<?php

namespace Examples;

use Orlion\CatAgentPhp\CatAgentContext;

class CatAgentContextImpl implements CatAgentContext
{
    private $map = [];

    public function addProperty(string $key, string $value): void
    {
        $this->map[$key] = $value;
    }

    public function getProperty(string $key): string
    {
        return $this->map[$key] ?? '';
    }
}
