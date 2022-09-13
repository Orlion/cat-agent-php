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

    public function start(Transaction $transaction): void
    {
        $this->ctx->start($transaction);
    }

    public function add(Message $message): void
    {
        $this->ctx->add($message);
    }

    public function flush(MessageTree $tree, bool $clearContext): void
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

    public function __construct(string $domain)
    {
        $this->tree = new DefaultMessageTree();
        $this->stack = new SplStack();

        $this->tree->setDomain($domain);

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

            $this->addTransactionChild($message, $parent);
        }
    }

    private function addTransactionChild(Message $message, Transaction $transaction): void
    {
        $transaction->addChild($transaction);
        $this->length++;
    }

    public function start(Transaction $transaction)
    {
        if (!$this->stack->isEmpty()) {
            $parent = $this->stack->top();
            $this->addTransactionChild($transaction, $parent);
        } else {
            $this->tree->setMessage($transaction);
        }

        $this->stack->push($transaction);
    }
}

