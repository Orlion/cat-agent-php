<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Io\TcpSocketSender;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\Transaction;

class DefaultMessageManager implements MessageManager
{
    private $context;
    private $sender;

    private function __construct(string $domain)
    {
        $this->context = new Context($domain);
        $this->sender = TcpSocketSender::getInstance();
    }

    public function start(Transaction $transaction): void
    {
        $this->context->start($transaction);
    }

    public function end(Transaction $transaction): void
    {
        $this->context->end($this, $transaction);
    }

    public function add(Message $message): void
    {
        $this->context->add($this, $message);
    }

    public function flush(): void
    {

    }
}