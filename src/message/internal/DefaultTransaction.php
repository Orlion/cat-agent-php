<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Util\Time;

class DefaultTransaction extends AbstractMessage implements Transaction
{
    private $durationInMicro = -1;
    private $durationStart = 0;
    private $children = [];
    private $manager;

    public function __construct(string $type, string $name, MessageManager $manager)
    {
        parent::__construct($type, $name);

        $this->manager = $manager;
        $this->durationStart = Time::currentTimeMicro();
    }

    public function addChild(Message $message): Transaction
    {
        $this->children[] = $message;
        return $this;
    }

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

    public function getChildren(): array
    {
        return $this->children;
    }

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

    public function getDurationInMillis(): int
    {
        return $this->getDurationInMicros() / 1000;
    }

    public function getRawDurationInMicros(): int
    {
        return $this->durationInMicro;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function setDurationInMicros(int $durationInMicros): void
    {
        $this->durationInMicro = $durationInMicros;
    }

    public function setDurationInMillis(int $durationInMills): void
    {
        $this->durationInMicro = $durationInMills * 1000;
    }

    public function setDurationStart(int $durationStart): void
    {
        $this->durationStart = $durationStart;
    }
}