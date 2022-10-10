<?php

namespace Orlion\CatAgentPhp;

use Orlion\CatAgentPhp\Exception\CatAgentException;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageProducer;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageManager;
use Orlion\CatAgentPhp\Message\Io\CatAgentClient;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Util\Time;

class CatAgent
{
    private static $producer;
    private static $manager;
    private static $enabled = true;
    private static $init = false;

    public static function init(string $domain, string $serverAddr): void
    {
        if (!self::$init) {
            $client = new CatAgentClient($serverAddr);
            self::$manager = new DefaultMessageManager($domain, $client);
            self::$producer = new DefaultMessageProducer(self::$manager, $client, $domain);
            
            self::$init = true;
        }
    }
    
    private static function checkInitialize(): void
    {
        if (!self::$init) {
            throw new CatAgentException('cat has not been initialized');
        }
    }

    public static function enable(): void
    {
        self::$enabled = true;
    }

    public static function disable(): void
    {
        self::$enabled = false;
    }

    public static function getManager(): MessageManager
    {
        self::checkInitialize();

        return self::$manager;
    }

    public static function getProducer(): MessageProducer
    {
        self::checkInitialize();

        return self::$producer;
    }

    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    public static function logEvent(string $type, string $name, string $status = Message::SUCCESS, $data = null): void
    {
        if (self::isEnabled()) {
            CatAgent::getProducer()->logEvent($type, $name, $status, $data);
        }
    }

    public static function newEvent(string $type, string $name): Event
    {
        if (self::isEnabled()) {
            return CatAgent::getProducer()->newEvent($type, $name);
        }
    }

    public static function newTransaction(string $type, string $name): Transaction
    {
        if (self::isEnabled()) {
            return CatAgent::getProducer()->newTransaction($type, $name);
        }
    }

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
    }

    public static function logRemoteCallClient(CatAgentContext $ctx, string $domain = 'default')
    {
        if (self::isEnabled()) {
            $tree = CatAgent::getManager()->getMessageTree();
            if (!is_null($tree)) {
                $messageId = $tree->getMessageId();
                if (is_null($messageId)) {
                    $messageId = CatAgent::getProducer()->createMessageId();
                    $tree->setMessageId($messageId);
                }
    
                $childId = CatAgent::getProducer()->createRpcMessageId($domain);
                CatAgent::logEvent(CatAgentConstants::TYPE_REMOTE_CALL, '', Event::SUCCESS, $childId);
    
                $root = $tree->getRootMessageId();
                if (is_null($root)) {
                    $root = $messageId;
                }
    
                $ctx->addProperty($ctx::ROOT, $root);
                $ctx->addProperty($ctx::PARENT, $messageId);
                $ctx->addProperty($ctx::CHILD, $childId);
            }
        }
    }

    public static function logRemoteCallServer(CatAgentContext $ctx)
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

    private function __construct()
    {
        
    }
}