<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Io\CatAgentClient;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;

class DefaultMessageProducer implements MessageProducer
{
    private $manager;
    private $client;
    private $domain;

    public function __construct(MessageManager $manager, CatAgentClient $client, string $domain)
    {
        $this->manager = $manager;
        $this->client = $client;
        $this->domain = $domain;
    }

    public function createMessageId(): string
    {
        return $this->client->createMessageId($this->domain);
    }

    public function createRpcMessageId(string $domain): string
    {
        return $this->client->createMessageId($domain);
    }

    public function logEvent(string $type, string $name, string $status = Message::SUCCESS, array $data = []): void
    {
        $event = $this->newEvent($type, $name);

        if (count($data) > 0) {
            $event->setData($data);
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

