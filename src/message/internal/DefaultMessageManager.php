<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Exception;
use Orlion\CatAgentPhp\Exception\IoException;
use Orlion\CatAgentPhp\Message\Io\TcpSocket;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\MessageTree;
use Orlion\CatAgentPhp\Message\Transaction;
use SplStack;

/**
 * DefaultMessageManager
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
class DefaultMessageManager implements MessageManager
{
    /**
     * @var DefaultMessageTree
     */
    private $tree;
    /**
     * @var SplStack
     */
    private $stack;
    /**
     * @var int
     */
    private $length;
    /**
     * @var TcpSocket
     */
    private $tcpSocket;
    /**
     * @var Exception
     */
    private $lastException;

    /**
     * @param string $domain
     * @param TcpSocket $tcpSocket
     */
    public function __construct(string $domain, TcpSocket $tcpSocket)
    {
        $this->stack = new SplStack();

        $pid = getmypid();
        $this->tree = new DefaultMessageTree();
        $this->tree->setDomain($domain);
        $this->tree->setThreadGroupName('PHP-GROUP');
        $this->tree->setThreadId($pid);
        $this->tree->setThreadName('PHP-' . $pid);

        $this->length = 1;

        $this->tcpSocket = $tcpSocket;
    }

    /**
     * @param Transaction $transaction
     * @return void
     */
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

    /**
     * @param Transaction $transaction
     * @return void
     */
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
                $this->flush();
            }
        }
    }

    /**
     * @param Message $message
     * @return void
     */
    public function add(Message $message): void
    {
        if ($this->stack->isEmpty()) {
            $this->tree->setMessage($message);
            $this->flush();
        } else {
            $parent = $this->stack->top();

            $this->addTransactionChild($message, $parent);
        }
    }

    /**
     * @param Message $message
     * @param Transaction $transaction
     * @return void
     */
    private function addTransactionChild(Message $message, Transaction $transaction): void
    {
        $transaction->addChild($message);
        $this->length++;
    }

    /**
     * @return void
     */
    protected function flush(): void
    {
        try {
            $this->tcpSocket->send($this->tree);
        } catch (IoException $e) {
            $this->lastException = $e;
        }
        $this->resetTree();
    }

    /**
     * @return void
     */
    protected function resetTree()
    {
        $this->tree->setMessage(null);
        $this->tree->setMessageId(null);
        $this->tree->setParentMessageId(null);
        $this->tree->setRootMessageId(null);
    }

    /**
     * @return MessageTree|null
     */
    public function getMessageTree(): ?MessageTree
    {
        return $this->tree;
    }

    /**
     * @return Exception|null
     */
    public function getLastException(): ?Exception
    {
        return $this->lastException;
    }
}