<?php

namespace Orlion\CatAgentPhp\Message;

/**
 * Message
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message
 */
interface Message
{
    /**
     *
     */
    const SUCCESS = '0';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return bool
     */
    public function isCompleted(): bool;

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void;

    /**
     * @return void
     */
    public function setSuccessStatus(): void;

    /**
     * @param int $timestamp
     * @return void
     */
    public function setTimestamp(int $timestamp): void;

    /**
     * @param $data
     * @return void
     */
    public function setData($data): void;

    /**
     * @param $data
     * @return void
     */
    public function addData($data): void;

    /**
     * @return void
     */
    public function complete(): void;
}
