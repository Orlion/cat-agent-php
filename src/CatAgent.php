<?php

namespace Orlion\CatAgentPhp;

use Exception;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageProducer;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageManager;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Util\Time;
use RuntimeException;

class CatAgent
{
    private static $producer;
    private static $manager;
    private static $enabled = true;
    private static $init = false;

    public static function init(string $domain, string $server): void
    {
        if (!self::$init) {
            try {
                self::$manager = new DefaultMessageManager($domain, $server);
                self::$producer = new DefaultMessageProducer(self::$manager);
                
                self::$init = true;
            } catch (Exception $e) {
                self::disable();
            }
        }
    }
    
    private static function checkInitialize(): void
    {
        if (!self::$init) {
            throw new RuntimeException('Cat has not been initialized, please execute CatAgent::init() first.');
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

    public static function logEvent(string $type, string $name, string $status = '', string $nameValuePairs = ''): void
    {
        if (self::isEnabled()) {
            CatAgent::getProducer()->logEvent($type, $name, $status, $nameValuePairs);
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

    public static function logRemoteCallClient(CatAgentContext $ctx, string $domain)
    {
        if (self::isEnabled()) {
            $tree = CatAgent::getManager()->getMessageTree();
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

    public static function logRemoteCallServer(CatAgentContext $ctx)
    {
        if (self::isEnabled()) {
            $tree = CatAgent::getManager()->getMessageTree();
            $childId = $ctx->getProperty(CatAgentContext::CHILD);
            $rootId = $ctx->getProperty(CatAgentContext::ROOT);
            $parentId = $ctx->getProperty(CatAgentContext::PARENT);

            if ($parentId === '') {
                $tree->setParentMessageId($parentId);
            }
            if ($rootId === '') {
                $tree->setRootMessageId($rootId);
            }
            if ($childId === '') {
                $tree->setMessageId($childId);
            }
        }
    }

    private function __construct()
    {
        
    }
}