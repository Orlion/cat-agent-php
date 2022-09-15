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

    private $transactions;
    private $events;
    private $metrics;

    public function clearMessageList()
    {
        $this->transactions = [];

        $this->events = [];

        $this->metrics = [];
    }

    public function copy(): MessageTree
    {
        $tree = new DefaultMessageTree();

        $tree->setDomain($this->domain);
        $tree->setMessageId($this->messageId);
        $tree->setParentMessageId($this->parentMessageId);
        $tree->setRootMessageId($this->rootMessageId);
        $tree->setThreadGroupName($this->threadGroupName);
        $tree->setThreadId($this->threadId);
        $tree->setThreadName($this->threadName);
        $tree->setMessage($this->message);
        return $tree;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getParentMessageId(): string
    {
        return $this->parentMessageId;
    }

    public function getRootMessageId(): string
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

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function setMessage(?Message $message): void
    {
        $this->message = $message;
    }

    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    public function setParentMessageId(string $parentMessageId): void
    {
        $this->parentMessageId = $parentMessageId;
    }

    public function setRootMessageId(string $rootMessageId): void
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
