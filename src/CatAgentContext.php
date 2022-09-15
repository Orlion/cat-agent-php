<?php

namespace Orlion\CatAgentPhp;

interface CatAgentContext
{
    const ROOT = "_catRootMessageId";

    const PARENT = "_catParentMessageId";

    const CHILD = "_catChildMessageId";

    public function addProperty(string $key, string $value): void;

    public function getProperty(string $key): string;
}