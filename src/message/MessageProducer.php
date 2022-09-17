<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageProducer
{
    public function createMessageId(): string;

    public function createRpcMessageId(string $domain): string;

    public function logEvent(string $type, string $name, string $status = '', array $data = []): void;

    public function newEvent(string $type, string $name): Event;

    public function newTransaction(string $type, string $name): Transaction;
}