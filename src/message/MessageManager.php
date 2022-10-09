<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageManager {
    public function add(Message $message): void;

    public function end(Transaction $transaction): void;

    public function start(Transaction $transaction): void;

    public function getMessageTree(): ?MessageTree;
}