<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Metric;
use Orlion\CatAgentPhp\Message\Transaction;
use Throwable;

class DefaultMessageProducer implements MessageProducer
{
    private $manager;
    private $factory;

    private function __construct(MessageManager $manager)
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

    public function logError(Throwable $cause, string $message = ''): void
    {

    }

    public function logErrorWithCategory(string $category, Throwable $cause, string $message = ''): void
    {
        
    }

    public function logEvent(string $type, string $name, string $status = '', string $nameValuePairs = ''): void
    {
        
    }

    public function logMetric(string $type, string $name, string $nameValuePairs): void
    {
        
    }

    public function newEvent(string $type, string $name): Event
    {
        return new DefaultEvent($type, $name, $this->manager);
    }

    public function newMetric(string $type, string $name): Metric
    {
        return new DefaultMetric($type, $name, $this->manager);
    }

    public function newTransaction(string $type, string $name): Transaction
    {
        $transaction = new DefaultTransaction($type, $name, $this->manager);

        $this->manager->start($transaction);
        return $transaction;
    }
}

