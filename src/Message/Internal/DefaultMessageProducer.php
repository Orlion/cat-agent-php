<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Exception\IoException;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Io\TcpSocket;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;

/**
 * DefaultMessageProducer
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
class DefaultMessageProducer implements MessageProducer
{
    /**
     * @var MessageManager
     */
    private $manager;
    /**
     * @var TcpSocket
     */
    private $tcpSocket;
    /**
     * @var string
     */
    private $domain;

    /**
     * @param MessageManager $manager
     * @param TcpSocket $tcpSocket
     * @param string $domain
     */
    public function __construct(MessageManager $manager, TcpSocket $tcpSocket, string $domain)
    {
        $this->manager = $manager;
        $this->tcpSocket = $tcpSocket;
        $this->domain = $domain;
    }

    /**
     * @return string
     * @throws IoException
     */
    public function createMessageId(): string
    {
        return $this->tcpSocket->createMessageId($this->domain);
    }

    /**
     * @param string $domain
     * @return string
     * @throws IoException
     */
    public function createRpcMessageId(string $domain): string
    {
        return $this->tcpSocket->createMessageId($domain);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $status
     * @param $data
     * @return void
     */
    public function logEvent(string $type, string $name, string $status = Message::SUCCESS, $data = null): void
    {
        $event = $this->newEvent($type, $name);

        if (!is_null($data)) {
            $event->setData($data);
        }

        $event->setStatus($status);
        $event->complete();
    }

    /**
     * @param string $type
     * @param string $name
     * @return Event
     */
    public function newEvent(string $type, string $name): Event
    {
        return new DefaultEvent($type, $name, $this->manager);
    }

    /**
     * @param string $type
     * @param string $name
     * @return Transaction
     */
    public function newTransaction(string $type, string $name): Transaction
    {
        $transaction = new DefaultTransaction($type, $name, $this->manager);

        $this->manager->start($transaction);
        return $transaction;
    }
}

