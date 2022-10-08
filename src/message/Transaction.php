<?php

namespace Orlion\CatAgentPhp\Message;

interface Transaction extends Message
{
    public function addChild(Message $message): Transaction;

    public function getChildren(): array;

    public function getDurationInMicros(): int;

    public function getDurationInMillis(): int;

    public function getRawDurationInMicros(): int;

    public function hasChildren(): bool;

    public function setDurationInMicros(int $durationInMicros): void;

    public function setDurationInMillis(int $durationInMills): void;

    public function setDurationStart(int $durationStart): void;
}
