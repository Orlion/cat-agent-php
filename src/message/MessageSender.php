<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageSender {
    public function send(MessageTree $tree);
}