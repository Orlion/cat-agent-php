<?php

namespace Orlion\CatAgentPhp\Message;

interface MessageTree 
{
    public function getDomain(): string;

    public function getMessage(): ?Message;

    public function getMessageId(): ?string;

    public function getParentMessageId(): ?string;

    public function getRootMessageId(): ?string;

    public function getThreadGroupName(): string;

    public function getThreadId(): string;

    public function getThreadName(): string;

    public function setDomain(string $domain): void;

    public function setMessage(?Message $message): void;

    public function setMessageId(?string $messageId): void;

    public function setParentMessageId(?string $parentMessageId): void;

    public function setRootMessageId(?string $rootMessageId): void;

    public function setThreadGroupName(string $name): void;

    public function setThreadId(string $threadId): void;

    public function setThreadName(string $name): void;
}
