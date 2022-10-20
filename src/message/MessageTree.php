<?php

namespace Orlion\CatAgentPhp\Message;

/**
 * MessageTree
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message
 */
interface MessageTree
{
    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message;

    /**
     * @return string|null
     */
    public function getMessageId(): ?string;

    /**
     * @return string|null
     */
    public function getParentMessageId(): ?string;

    /**
     * @return string|null
     */
    public function getRootMessageId(): ?string;

    /**
     * @return string
     */
    public function getThreadGroupName(): string;

    /**
     * @return string
     */
    public function getThreadId(): string;

    /**
     * @return string
     */
    public function getThreadName(): string;

    /**
     * @param string $domain
     * @return void
     */
    public function setDomain(string $domain): void;

    /**
     * @param Message|null $message
     * @return void
     */
    public function setMessage(?Message $message): void;

    /**
     * @param string|null $messageId
     * @return void
     */
    public function setMessageId(?string $messageId): void;

    /**
     * @param string|null $parentMessageId
     * @return void
     */
    public function setParentMessageId(?string $parentMessageId): void;

    /**
     * @param string|null $rootMessageId
     * @return void
     */
    public function setRootMessageId(?string $rootMessageId): void;

    /**
     * @param string $name
     * @return void
     */
    public function setThreadGroupName(string $name): void;

    /**
     * @param string $threadId
     * @return void
     */
    public function setThreadId(string $threadId): void;

    /**
     * @param string $name
     * @return void
     */
    public function setThreadName(string $name): void;
}
