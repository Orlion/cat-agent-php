<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageTree 
{
    public function canDiscard(): bool;

    public function copy(): MessageTree;

    public function getBuffer(): array;

    public function getDomain(): string;

    public function getEvents(): array;

    public function getHeartbeats(): array;

    public function getHostName(): string;

    public function getIpAddress(): string;

    public function getMessage(): Message;

    public function getMetrics(): array;

    public function getParentMessageId(): string;

    public function getRootMessageId(): string;

    public function getSessionToken(): string;

    public function getTransactions(): array;

    public function isHitSample(): bool;

    public function setDiscardPrivate(bool $discard): void;

    public function setDomain(string $domain): void;

    public function setHitSample(bool $hitSample): void;

    public function setMessage(Message $message): void;

    public function setParentMessageId(string $parentMessageId): void;

    public function setRootMessageId(string $rootMessageId): void;

    public function setSessionToken(string $session): void;
}
