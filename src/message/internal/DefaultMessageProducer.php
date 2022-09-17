<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Metric;
use Orlion\CatAgentPhp\Message\Transaction;
use Throwable;

class DefaultMessageProducer implements MessageProducer
{
    private $manager;
    private $factory;

    public function __construct(MessageManager $manager)
    {
        $this->manager = $manager;
        $this->factory = MessageIdFactory::getInstance();
    }

    public function createMessageId(): string
    {
        return $this->factory->getNextId();
    }

    public function createRpcMessageId(string $domain): string
    {
        return $this->factory->getNextId($domain);
    }

    public function logEvent(string $type, string $name, string $status = Message::SUCCESS, array $keyValuePairs = []): void
    {
        $event = $this->newEvent($type, $name);

        if (empty($keyValuePairs)) {
            $event->addData($keyValuePairs);
        }

        $event->setStatus($status);
        $event->complete();
    }

    public function newEvent(string $type, string $name): Event
    {
        return new DefaultEvent($type, $name, $this->manager);
    }

    public function newTransaction(string $type, string $name): Transaction
    {
        $transaction = new DefaultTransaction($type, $name, $this->manager);

        $this->manager->start($transaction);
        return $transaction;
    }
}

