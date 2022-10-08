<?php

namespace Orlion\CatAgentPhp\Message;

interface Message
{
    const SUCCESS = '0';

    public function getType(): string;

    public function getName(): string;

    public function getStatus(): string;

    public function isSuccess(): bool;

    public function getTimestamp(): int;

    public function getData(): ?array;

    public function isCompleted(): bool;

    public function setType(string $type): void;

    public function setName(string $name): void;

    public function setStatus(string $status): void;

    public function setSuccessStatus(): void;

    public function setTimestamp(int $timestamp): void;

    public function setData(array $data): void;

    public function addData(array $data): void;

    public function complete(): void;
}
