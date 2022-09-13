<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Heartbeat;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageTree;

class DefaultMessageTree implements MessageTree
{
    private $buf;
    private $domain;
    private $hostName;
    private $ipAddress;
    private $message;
    private $parentMessageId;
    private $rootMessageId;
    private $sessionToken;
    private $threadGroupName;
    private $threadId;
    private $threadName;
    private $discard = true;
    private $hitSample = false;

    private $transactions;
    private $events;
    private $heartbeats;
    private $metrics;

    public function addHeartbeat(Heartbeat $heartbeat)
    {
        if (is_null($this->heartbeats)) {
            $this->heartbeats = [$heartbeat];
        } else {
            $this->heartbeats[] = $heartbeat;
        }
    }

    public function canDiscard(): bool
    {
        return $this->discard;
    }

    public function clearMessageList()
    {
        $this->transactions = [];

        $this->events = [];

        $this->heartbeats = [];

        $this->metrics = [];
    }

    public function copy(): MessageTree
    {
        $tree = new DefaultMessageTree();

        return $tree;
    }

    public function getBuffer(): array
    {
        return $this->buf;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getHostName(): string
    {
        return $this->hostName;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
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

    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }

    public function isHitSample(): bool
    {
        return $this->hitSample;
    }

    public function setBuffer(array $buf): void
    {
        $this->buf = $buf;
    }

    public function setDiscardPrivate(bool $discard): void
    {
        $this->discard = $discard;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function setHitSample(bool $hitSample): void
    {
        $this->hitSample = $hitSample;
    }

    public function setHostName(string $hostName): void
    {
        $this->hostName = $hostName;
    }

    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function setMessage(Message $message): void
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

    public function setSessionToken(string $session): void
    {
        $this->sessionToken = $session;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getHeartbeats(): array
    {
        return $this->heartbeats;
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