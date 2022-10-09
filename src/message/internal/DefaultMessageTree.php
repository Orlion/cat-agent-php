<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageTree;

class DefaultMessageTree implements MessageTree
{
    private $domain;
    private $message;
    private $messageId;
    private $parentMessageId;
    private $rootMessageId;
    private $threadGroupName;
    private $threadId;
    private $threadName;

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getParentMessageId(): ?string
    {
        return $this->parentMessageId;
    }

    public function getRootMessageId(): ?string
    {
        return $this->rootMessageId;
    }

    public function getThreadGroupName(): string
    {
        return $this->threadGroupName;
    }

    public function getThreadId(): string
    {
        return $this->threadId;
    }

    public function getThreadName(): string
    {
        return $this->threadName;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function setMessage(?Message $message): void
    {
        $this->message = $message;
    }

    public function setMessageId(?string $messageId): void
    {
        $this->messageId = $messageId;
    }

    public function setParentMessageId(?string $parentMessageId): void
    {
        $this->parentMessageId = $parentMessageId;
    }

    public function setRootMessageId(?string $rootMessageId): void
    {
        $this->rootMessageId = $rootMessageId;
    }

    public function setThreadGroupName(string $name): void
    {
        $this->threadGroupName = $name;
    }

    public function setThreadId(string $threadId): void
    {
        $this->threadId = $threadId;
    }

    public function setThreadName(string $name): void
    {
        $this->threadName = $name;
    }
}