<?php

namespace Orlion\CatAgentPhp\Message;

/**
 * MessageProducer
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message
 */
interface MessageProducer
{
    /**
     * @return string
     */
    public function createMessageId(): string;

    /**
     * @param string $domain
     * @return string
     */
    public function createRpcMessageId(string $domain): string;

    /**
     * @param string $type
     * @param string $name
     * @param string $status
     * @param $data
     * @return void
     */
    public function logEvent(string $type, string $name, string $status = Message::SUCCESS, $data = null): void;

    /**
     * @param string $type
     * @param string $name
     * @return Event
     */
    public function newEvent(string $type, string $name): Event;

    /**
     * @param string $type
     * @param string $name
     * @return Transaction
     */
    public function newTransaction(string $type, string $name): Transaction;
}