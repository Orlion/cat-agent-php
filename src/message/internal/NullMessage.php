<?php
/*
 * This file is part of the CatAgentPhp package.
 *
 * (c) Orlion <orlionml@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\Transaction;

/**
 * NullMessage
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
class NullMessage implements Transaction, Event
{
    /**
     *
     */
    const DEFAULT = '';

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::DEFAULT;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::DEFAULT;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return self::DEFAULT;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return 0;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return true;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {

    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {

    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {

    }

    /**
     * @return void
     */
    public function setSuccessStatus(): void
    {

    }

    /**
     * @param int $timestamp
     * @return void
     */
    public function setTimestamp(int $timestamp): void
    {

    }

    /**
     * @param $data
     * @return void
     */
    public function setData($data): void
    {

    }

    /**
     * @param $data
     * @return void
     */
    public function addData($data): void
    {

    }

    /**
     * @return void
     */
    public function complete(): void
    {

    }

    /**
     * @param Message $message
     * @return Transaction
     */
    public function addChild(Message $message): Transaction
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return [];
    }

    /**
     * @return int
     */
    public function getDurationInMicros(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getDurationInMillis(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getRawDurationInMicros(): int
    {
        return 0;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return false;
    }

    /**
     * @param int $durationInMicros
     * @return void
     */
    public function setDurationInMicros(int $durationInMicros): void
    {

    }

    /**
     * @param int $durationInMills
     * @return void
     */
    public function setDurationInMillis(int $durationInMills): void
    {

    }

    /**
     * @param int $durationStart
     * @return void
     */
    public function setDurationStart(int $durationStart): void
    {

    }
}