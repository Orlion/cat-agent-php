<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageTree;
use Orlion\CatAgentPhp\Message\Transaction;
use SplStack;

class DefaultMessageManager implements MessageManager
{
    private static $instance;
    private $context;

    private function __construct()
    {
        $this->context = new Context();
    }

    public static function getInstance(): DefaultMessageManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new DefaultMessageManager();
        }
        
        return self::$instance;
    }

    public function hasContext(): bool
    {
        return false;
    }

    public function setup(): void
    {
        
    }

    public function start(Transaction $transaction): void
    {

    }

    public function add(Message $message)
    {

    }
}

class Context
{
    const HOUR = 3600 * 1000;
    const SIZE = 2000;

    private $tree;
    private $stack;
    private $length;
    private $traceMode;
    private $totalDurationInMicros;
    private $knownExceptions;

    public function __construct(string $domain, string $hostName, string $ipAddress)
    {
        $this->tree = new DefaultMessageTree();
        $this->stack = new SplStack();

        $this->tree->setDomain($domain);
        $this->tree->setHostName($hostName);
        $this->tree->setIpAddress($ipAddress);

        $this->length = 1;
    }

    public function add(Message $message): void
    {
        if ($this->stack->isEmpty()) {
            $tree = $this->tree->copy();

            $tree->setMessage($message);
            // todo: $this->flush($tree, true);
        } else {
            $parent = $this->stack->top();

            
        }
    }

    private function addTransactionChild(Message $message, Transaction $transaction): void
    {
        $treePeriod = $this->tree->getMessage()->getTimestamp() / self::HOUR;
        $messagePeriod = ($message->getTimestamp() - 10 * 1000) / self::HOUR;

        if ($treePeriod < $messagePeriod || $this->length >= self::SIZE) {

        }
    }
}

class TransactionHelper
{
    public function truncateAndFlush(Context $ctx, int $timestamp): void
    {

    }
}