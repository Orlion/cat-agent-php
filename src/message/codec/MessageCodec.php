<?php

namespace Orlion\CatAgentPhp\Message\Codec;

use Orlion\CatAgentPhp\Message\MessageTree;

interface MessageCodec
{
    public function encode(MessageTree $tree): string;
}