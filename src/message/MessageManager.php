<?php

namespace Orlion\CatAgentPhp\Message;

use Exception;

/**
 * MessageManager
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message
 */
interface MessageManager {
    /**
     * @param Message $message
     * @return void
     */
    public function add(Message $message): void;

    /**
     * @param Transaction $transaction
     * @return void
     */
    public function end(Transaction $transaction): void;

    /**
     * @param Transaction $transaction
     * @return void
     */
    public function start(Transaction $transaction): void;

    /**
     * @return MessageTree|null
     */
    public function getMessageTree(): ?MessageTree;

    /**
     * @return Exception|null
     */
    public function getLastException(): ?Exception;
}