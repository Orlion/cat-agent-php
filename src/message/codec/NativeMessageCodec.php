<?php

namespace Orlion\CatAgentPhp\Message\Codec;

use Orlion\CatAgentPhp\Message\MessageTree;

class NativeMessageCodec implements MessageCodec
{
    const ID = 'NT1';

    public function encode(MessageTree $tree): string
    {
        return '';
    }
}