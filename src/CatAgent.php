<?php

namespace Orlion\CatAgentPhp;

use Exception;
use Orlion\CatAgentPhp\Exception\CatAgentException;
use Orlion\CatAgentPhp\Exception\IoException;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Internal\NullMessage;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageProducer;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageManager;
use Orlion\CatAgentPhp\Message\Io\TcpSocket;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Util\Time;

/**
 * CatAgent
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp
 */
class CatAgent
{
    /**
     * @var
     */
    private static $producer;
    /**
     * @var
     */
    private static $manager;
    /**
     * @var bool
     */
    private static $enabled = true;
    /**
     * @var bool
     */
    private static $init = false;
    /**
     * @var Exception
     */
    private static $lastException;

    /**
     * Initialize cat agent
     *
     * @param string $domain application domain
     * @param string $serverAddr cat agent's address, for example, unix:///var/run/cat-agent.sock
     * @return void
     * @throws IoException
     */
    public static function init(string $domain, string $serverAddr): void
    {
        if (!self::$init) {
            $tcpSocket = new TcpSocket($serverAddr);
            self::$manager = new DefaultMessageManager($domain, $tcpSocket);
            self::$producer = new DefaultMessageProducer(self::$manager, $tcpSocket, $domain);
            
            self::$init = true;
        }
    }

    /**
     * @return void
     * @throws CatAgentException
     */
    private static function checkInitialize(): void
    {
        if (!self::$init) {
            throw new CatAgentException('cat agent has not been initialized');
        }
    }

    /**
     * @return void
     */
    public static function enable(): void
    {
        self::$enabled = true;
    }

    /**
     * @return void
     */
    public static function disable(): void
    {
        self::$enabled = false;
    }

    /**
     * @return MessageManager
     * @throws CatAgentException
     */
    public static function getManager(): MessageManager
    {
        self::checkInitialize();

        return self::$manager;
    }

    /**
     * @return MessageProducer
     * @throws CatAgentException
     */
    public static function getProducer(): MessageProducer
    {
        self::checkInitialize();

        return self::$producer;
    }

    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $status
     * @param mixed $data
     * @return void
     * @throws CatAgentException
     */
    public static function logEvent(string $type, string $name, string $status = Message::SUCCESS, $data = null): void
    {
        if (self::isEnabled()) {
            CatAgent::getProducer()->logEvent($type, $name, $status, $data);
        }
    }

    /**
     * @param string $type
     * @param string $name
     * @return Event
     * @throws CatAgentException
     */
    public static function newEvent(string $type, string $name): Event
    {
        if (self::isEnabled()) {
            return CatAgent::getProducer()->newEvent($type, $name);
        }

        return new NullMessage();
    }

    /**
     * @param string $type
     * @param string $name
     * @return Transaction
     * @throws CatAgentException
     */
    public static function newTransaction(string $type, string $name): Transaction
    {
        if (self::isEnabled()) {
            return CatAgent::getProducer()->newTransaction($type, $name);
        }

        return new NullMessage();
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $duration
     * @return Transaction
     * @throws CatAgentException
     */
    public static function newTransactionWithDuration(string $type, string $name, int $duration): Transaction
    {
        if (self::isEnabled()) {
            $transaction = CatAgent::getProducer()->newTransaction($type, $name);

            $transaction->setDurationInMillis($duration);

            if ($duration < 60 * 1000) {
                $transaction->setTimestamp(Time::currentTimeMillis() - $duration);
            }

            return $transaction;
        }

        return new NullMessage();
    }

    /**
     * @param CatAgentContext $ctx
     * @param string $domain
     * @return void
     * @throws CatAgentException
     */
    public static function logRemoteCallClient(CatAgentContext $ctx, string $domain = 'default'): void
    {
        if (self::isEnabled()) {
            $tree = CatAgent::getManager()->getMessageTree();
            if (!is_null($tree)) {
                $messageId = $tree->getMessageId();
                try {
                    if (is_null($messageId)) {
                        $messageId = CatAgent::getProducer()->createMessageId();
                        $tree->setMessageId($messageId);
                    }

                    $childId = CatAgent::getProducer()->createRpcMessageId($domain);
                    CatAgent::logEvent(CatAgentConstants::TYPE_REMOTE_CALL, '', Message::SUCCESS, $childId);

                    $root = $tree->getRootMessageId();
                    if (is_null($root)) {
                        $root = $messageId;
                    }

                    $ctx->addProperty($ctx::ROOT, $root);
                    $ctx->addProperty($ctx::PARENT, $messageId);
                    $ctx->addProperty($ctx::CHILD, $childId);
                } catch (IoException $e) {
                    self::$lastException = $e;
                }
            }
        }
    }

    /**
     * @param CatAgentContext $ctx
     * @return void
     * @throws CatAgentException
     */
    public static function logRemoteCallServer(CatAgentContext $ctx): void
    {
        if (self::isEnabled()) {
            $tree = CatAgent::getManager()->getMessageTree();
            if (!is_null($tree)) {
                $childId = $ctx->getProperty(CatAgentContext::CHILD);
                $rootId = $ctx->getProperty(CatAgentContext::ROOT);
                $parentId = $ctx->getProperty(CatAgentContext::PARENT);
    
                if ($parentId !== '') {
                    $tree->setParentMessageId($parentId);
                }
                if ($rootId !== '') {
                    $tree->setRootMessageId($rootId);
                }
                if ($childId !== '') {
                    $tree->setMessageId($childId);
                }
            }
        }
    }

    /**
     * @return Exception|null
     * @throws CatAgentException
     */
    public static function getLastException(): ?Exception
    {
        $managerLastException = self::getManager()->getLastException();
        if (!is_null($managerLastException)) {
            return $managerLastException;
        }

        return self::$lastException;
    }

    /**
     *
     */
    private function __construct()
    {
        
    }
}