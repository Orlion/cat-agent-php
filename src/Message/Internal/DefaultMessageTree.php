<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageTree;

/**
 * DefaultMessageTree
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
class DefaultMessageTree implements MessageTree
{
    /**
     * @var
     */
    private $domain;
    /**
     * @var
     */
    private $message;
    /**
     * @var
     */
    private $messageId;
    /**
     * @var
     */
    private $parentMessageId;
    /**
     * @var
     */
    private $rootMessageId;
    /**
     * @var
     */
    private $threadGroupName;
    /**
     * @var
     */
    private $threadId;
    /**
     * @var
     */
    private $threadName;

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @return string|null
     */
    public function getParentMessageId(): ?string
    {
        return $this->parentMessageId;
    }

    /**
     * @return string|null
     */
    public function getRootMessageId(): ?string
    {
        return $this->rootMessageId;
    }

    /**
     * @return string
     */
    public function getThreadGroupName(): string
    {
        return $this->threadGroupName;
    }

    /**
     * @return string
     */
    public function getThreadId(): string
    {
        return $this->threadId;
    }

    /**
     * @return string
     */
    public function getThreadName(): string
    {
        return $this->threadName;
    }

    /**
     * @param string $domain
     * @return void
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @param Message|null $message
     * @return void
     */
    public function setMessage(?Message $message): void
    {
        $this->message = $message;
    }

    /**
     * @param string|null $messageId
     * @return void
     */
    public function setMessageId(?string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @param string|null $parentMessageId
     * @return void
     */
    public function setParentMessageId(?string $parentMessageId): void
    {
        $this->parentMessageId = $parentMessageId;
    }

    /**
     * @param string|null $rootMessageId
     * @return void
     */
    public function setRootMessageId(?string $rootMessageId): void
    {
        $this->rootMessageId = $rootMessageId;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setThreadGroupName(string $name): void
    {
        $this->threadGroupName = $name;
    }

    /**
     * @param string $threadId
     * @return void
     */
    public function setThreadId(string $threadId): void
    {
        $this->threadId = $threadId;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setThreadName(string $name): void
    {
        $this->threadName = $name;
    }
}