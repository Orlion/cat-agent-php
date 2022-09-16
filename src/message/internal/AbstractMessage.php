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
    private $completed = false;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
        $this->timestampInMillis = Time::currentTimeMillis();
    }

    public function setData(string $str)
    {
        $this->data = $str;
    }

    public function addData(string $keyValuePairs): void
    {
        if (is_null($this->data)) {
            $this->data = $keyValuePairs;
        } else {
            $this->data .= '&' . $keyValuePairs;
        }
    }

    public function getData()
    {
        if (is_null($this->data)) {
            return '';
        } else {
            return $this->data;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTimestamp(): int
    {
        return $this->timestampInMillis;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function isSuccess(): bool
    {
        return $this->statusCode > 0;
    }

    public function setComplete(bool $completed): void
    {
        $this->completed = $completed;
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

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}