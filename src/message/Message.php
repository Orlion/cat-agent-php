<?php

namespace Orlion\CatAgentPhp\Message;

interface Message
{
    const SUCCESS = '0';

    public function addData(string $keyValuePairs): void;

    public function complete(): void;

    public function getData();

    public function getName(): string;

    public function getStatus(): string;

    public function getTimestamp(): int;

    public function getType(): string;

    public function isCompleted(): bool;

    public function isSuccess(): bool;

    public function setStatus(string $status): void;

    public function setSuccessStatus(): void;

    public function setTimestamp(int $timestamp): void;
}