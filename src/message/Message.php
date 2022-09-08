<?php

namespace Orlion\CatAgentPhp\Message;

interface Message
{
    const SUCCESS = '0';

    public function addData(string $key, string $value): void;

    public function complete(): void;

    public function getData();

    public function getName(): string;

    public function getStatus(): string;

    public function getTimestamp(): float;

    public function getType(): string;

    public function isCompleted(): bool;

    public function isSuccess(): bool;

    public function setStatsu(string $status): void;

    public function setSuccessStatus(): void;

    public function setTimestamp(float $timestamp): void;
}