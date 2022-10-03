<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageIdFactory {
    public function getNextId(): string;
}