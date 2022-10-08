<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Util\Time;

abstract class AbstractMessage implements Message
{
    protected $type;
    private $name;
    private $status = self::SUCCESS;
    private $statusCode = 1;
    private $timestampInMillis = 0;
    private $data;
    private $completed = false;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
        $this->timestampInMillis = Time::currentTimeMillis();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isSuccess(): bool
    {
        return $this->statusCode > 0;
    }

    public function getTimestamp(): int
    {
        return $this->timestampInMillis;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;

        if (self::SUCCESS == $this->status) {
            $this->statusCode = 1;
        } else {
            $this->statusCode = -1;
        }
    }

    public function setSuccessStatus(): void
    {
        $this->status = self::SUCCESS;
        $this->statusCode = 1;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestampInMillis = $timestamp;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(array $data): void
    {
        if (is_null($this->data)) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }
    }

    public function setComplete(bool $completed): void
    {
        $this->completed = $completed;
    }
}