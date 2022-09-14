<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Orlion\CatAgentPhp\Message\MessageTree;

interface MessageSender
{
    public function send(MessageTree $tree): void;
}