<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Util\Time;

/**
 * DefaultTransaction
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
class DefaultTransaction extends AbstractMessage implements Transaction
{
    /**
     * @var int
     */
    private $durationInMicro = -1;
    /**
     * @var int
     */
    private $durationStart;
    /**
     * @var array
     */
    private $children = [];
    /**
     * @var MessageManager
     */
    private $manager;

    /**
     * @param string $type
     * @param string $name
     * @param MessageManager $manager
     */
    public function __construct(string $type, string $name, MessageManager $manager)
    {
        parent::__construct($type, $name);

        $this->manager = $manager;
        $this->durationStart = Time::currentTimeMicro();
    }

    /**
     * @param Message $message
     * @return Transaction
     */
    public function addChild(Message $message): Transaction
    {
        $this->children[] = $message;
        return $this;
    }

    /**
     * @return void
     */
    public function complete(): void
    {
        if (!$this->isCompleted()) {
            if ($this->durationInMicro == -1) {
                $this->durationInMicro = Time::currentTimeMicro() - $this->durationStart;
            }
            $this->setComplete(true);

            $this->manager->end($this);
        }
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getDurationInMicros(): int
    {
        if ($this->durationInMicro > 0) {
            return $this->durationInMicro;
        } else {
            $duration = 0;
            $len = count($this->children);

            if ($len > 0) {
                $lastChild = $this->children[$len - 1];

                if ($lastChild instanceof Transaction) {
                    $duration = ($lastChild->getTimestamp() - $this->getTimestamp()) * 1000 + $lastChild->getRawDurationInMicros();
                } else {
                    $duration = ($lastChild->getTimestamp() - $this->getTimestamp()) * 1000;
                }
            }

            return $duration;
        }
    }

    /**
     * @return int
     */
    public function getDurationInMillis(): int
    {
        return $this->getDurationInMicros() / 1000;
    }

    /**
     * @return int
     */
    public function getRawDurationInMicros(): int
    {
        return $this->durationInMicro;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * @param int $durationInMicros
     * @return void
     */
    public function setDurationInMicros(int $durationInMicros): void
    {
        $this->durationInMicro = $durationInMicros;
    }

    /**
     * @param int $durationInMills
     * @return void
     */
    public function setDurationInMillis(int $durationInMills): void
    {
        $this->durationInMicro = $durationInMills * 1000;
    }

    /**
     * @param int $durationStart
     * @return void
     */
    public function setDurationStart(int $durationStart): void
    {
        $this->durationStart = $durationStart;
    }
}