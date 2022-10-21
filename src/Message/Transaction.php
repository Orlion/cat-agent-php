<?php

namespace Orlion\CatAgentPhp\Message;

/**
 * Transaction
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message
 */
interface Transaction extends Message
{
    /**
     * @param Message $message
     * @return Transaction
     */
    public function addChild(Message $message): Transaction;

    /**
     * @return array
     */
    public function getChildren(): array;

    /**
     * @return int
     */
    public function getDurationInMicros(): int;

    /**
     * @return int
     */
    public function getDurationInMillis(): int;

    /**
     * @return int
     */
    public function getRawDurationInMicros(): int;

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @param int $durationInMicros
     * @return void
     */
    public function setDurationInMicros(int $durationInMicros): void;

    /**
     * @param int $durationInMills
     * @return void
     */
    public function setDurationInMillis(int $durationInMills): void;

    /**
     * @param int $durationStart
     * @return void
     */
    public function setDurationStart(int $durationStart): void;
}
