<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\Transaction;

class Context
{
    const HOUR = 3600 * 1000;
    const SIZE = 2000;

    private $tree;
    private $stack;
    private $length;

    public function __construct(string $domain)
    {
        $this->tree = new DefaultMessageTree();
        $this->stack = new \SplStack();

        $this->tree->setDomain($domain);

        $this->length = 1;
    }

    public function add(DefaultMessageManager $manager, Message $message): void
    {
        if ($this->stack->isEmpty()) {
            $tree = $this->tree->copy();

            $tree->setMessage($message);
            $manager->flush($tree);
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

    public function end(DefaultMessageManager $manager, Transaction $transaction): bool
    {
        if (!$this->stack->isEmpty()) {
            $current = $this->stack->pop();

            if ($transaction != $current) {
                while ($transaction != $current && !$this->stack->isEmpty()) {
                    $current = $this->stack->pop();
                }
            }

            if ($this->stack->isEmpty()) {
                $tree = $this->tree->copy();
                $tree->setMessage(null);

                $manager->flush($this->tree);

                return true;
            }
        }

        return false;
    }
}