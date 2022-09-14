<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Heartbeat;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageTree;

class DefaultMessageTree implements MessageTree
{
    private $domain;
    private $message;
    private $parentMessageId;
    private $rootMessageId;

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
        $tree->setParentMessageId($this->parentMessageId);
        $tree->setRootMessageId($this->rootMessageId);
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

    public function getParentMessageId(): string
    {
        return $this->parentMessageId;
    }

    public function getRootMessageId(): string
    {
        return $this->rootMessageId;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function setMessage(?Message $message): void
    {
        $this->message = $message;
    }

    public function setParentMessageId(string $parentMessageId): void
    {
        $this->parentMessageId = $parentMessageId;
    }

    public function setRootMessageId(string $rootMessageId): void
    {
        $this->rootMessageId = $rootMessageId;
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
}