<?php

namespace Orlion\CatAgentPhp;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageProducer;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageManager;
use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\Trace;
use Orlion\CatAgentPhp\Util\Time;
use RuntimeException;
use Throwable;

class CatAgent
{
    private static $producer;
    private static $manager;
    private static $enabled;
    private static $init = false;

    public static function init(): void
    {
        if (!self::$init) {
            self::$producer = DefaultMessageProducer::getInstance();
            self::$manager = DefaultMessageManager::getInstance();

            self::$init = true;
        }
    }
    
    private static function checkInitialize(): void
    {
        if (!self::$init) {
            throw new RuntimeException('Cat has not been initialized, Please execute CatAgent::init() first');
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

    public static function logError(Throwable $cause, string $message = ''): void
    {
        if (self::isEnabled()) {
            CatAgent::getProducer()->logError($cause, $message);
        }
    }

    public static function logErrorWithCategory(string $category, Throwable $cause, string $message = ''): void
    {
        if (self::isEnabled()) {
            CatAgent::getProducer()->logErrorWithCategory($category, $cause, $message);
        }
    }

    public static function logEvent(string $type, string $name, string $status = '', string $nameValuePairs = ''): void
    {
        if (self::isEnabled()) {
            CatAgent::getProducer()->logEvent($type, $name, $status, $nameValuePairs);
        }
    }

    public static function logMetricForCount(string $name, int $quantity = 0): void
    {
        if (self::isEnabled()) {
            self::checkInitialize();

            // todo
        }
    }

    public static function logMetricForDuration(string $name, int $durationInMills, array $tags = []): void
    {
        if (self::isEnabled()) {
            self::checkInitialize();

            // todo
        }
    }

    public static function newEvent(string $type, string $name): Event
    {
        if (self::isEnabled()) {
            return CatAgent::getProducer()->newEvent($type, $name);
        }
    }

    public static function newTrace(string $type, string $name): Trace
    {
        if (self::isEnabled()) {
            return CatAgent::getProducer()->newTrace($type, $name);
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

    private function __construct()
    {
        
    }
}