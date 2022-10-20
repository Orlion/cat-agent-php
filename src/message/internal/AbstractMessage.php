<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Util\Time;

/**
 * AbstractMessage
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
abstract class AbstractMessage implements Message
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $status = self::SUCCESS;
    /**
     * @var int
     */
    protected $statusCode = 1;
    /**
     * @var int
     */
    protected $timestampInMillis = 0;
    /**
     * @var
     */
    protected $data;
    /**
     * @var bool
     */
    protected $completed = false;

    /**
     * @param string $type
     * @param string $name
     */
    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
        $this->timestampInMillis = Time::currentTimeMillis();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode > 0;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestampInMillis;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;

        if (self::SUCCESS === $this->status) {
            $this->statusCode = 1;
        } else {
            $this->statusCode = -1;
        }
    }

    /**
     * @return void
     */
    public function setSuccessStatus(): void
    {
        $this->status = self::SUCCESS;
        $this->statusCode = 1;
    }

    /**
     * @param int $timestamp
     * @return void
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestampInMillis = $timestamp;
    }

    /**
     * @param $data
     * @return void
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param $data
     * @return void
     */
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

    /**
     * @param bool $completed
     * @return void
     */
    public function setComplete(bool $completed): void
    {
        $this->completed = $completed;
    }
}