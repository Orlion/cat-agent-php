<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Io\TcpSocketSender;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageTree;
use Orlion\CatAgentPhp\Message\Transaction;

class DefaultMessageManager implements MessageManager
{
    const SIZE = 2000;
    
    private $tree;
    private $stack;
    private $length;
    private $sender;

    private function __construct(string $domain, string $server)
    {
        $this->tree = new DefaultMessageTree($domain);
        $this->stack = new \SplStack();

        $this->tree->setDomain($domain);
        $this->tree->setThreadGroupName('PHP-GROUP');
        $pid = getmypid();
        $this->tree->setThreadId($pid);
        $this->tree->setThreadName('PHP-' . $pid);
        $this->length = 1;

        $this->sender = new TcpSocketSender($server);
    }

    public function start(Transaction $transaction): void
    {
        if (!$this->stack->isEmpty()) {
            $parent = $this->stack->top();
            $this->addTransactionChild($transaction, $parent);
        } else {
            $this->tree->setMessage($transaction);
        }

        $this->stack->push($transaction);
    }

    public function end(Transaction $transaction): void
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

                $this->flush($this->tree);
            }
        }
    }

    public function add(Message $message): void
    {
        if ($this->stack->isEmpty()) {
            $tree = $this->tree->copy();

            $tree->setMessage($message);
            $this->flush($tree);
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

    public function flush(): void
    {
        $this->sender->send($this->tree);
    }

    public function getMessageTree(): MessageTree
    {
        return $this->tree;
    }
}