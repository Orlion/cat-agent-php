<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageManager {
    public function add(Message $message): void;

    public function end(Transaction $transaction): void;

    public function getDomain(): string;

    public function getPeekTransaction(): Transaction;

    public function getMessageTree(): MessageTree;

    public function hasContext(): bool;

    public function isCatEnabled(): bool;

    public function isMessageEnabled(): bool;

    public function isTraceMode(): bool;

    public function reset(): void;

    public function setTraceMode(bool $traceMode): void;

    public function setup(): void;

    public function start(Transaction $transaction): void;

}