<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Util\Time;

abstract class AbstractMessage implements Message
{
    protected $type;
    protected $name;
    protected $status = self::SUCCESS;
    protected $statusCode = 1;
    protected $timestampInMillis = 0;
    protected $data;
    protected $completed = false;

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

    public function getData()
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

        if (self::SUCCESS === $this->status) {
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

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function addData($data): void
    {
        if (is_null($this->data)) {
            $this->data = $data;
        } else {
            if (is_array($this->data)) {
                if (is_array($data)) {
                    $this->data = array_merge($this->data, $data);
                } else {
                    $this->data[] = $data;
                }
            } else {
                $this->data = [$this->data, $data];
            }
        }
    }

    public function setComplete(bool $completed): void
    {
        $this->completed = $completed;
    }
}