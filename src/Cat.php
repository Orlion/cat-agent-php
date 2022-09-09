<?php

namespace Orlion\CatAgentPhp;

use Orlion\CatAgentPhp\Configuration\Client\Entity\ClientConfig;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Transaction;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageProducer;
use Orlion\CatAgentPhp\Message\Internal\DefaultMessageManager;

class Cat
{
    private static $producer;
    private static $manager;
    private static $enabled;
    private static $init = false;

    public static function newTransaction(string $type, string $name): Transaction
    {
        if (self::isEnabled()) {
            return Cat::getProducer().newTransaction($type, $name);
        }
    }

    public static function getProducer(): MessageProducer
    {
        self::checkAndInitialize();
        return self::$producer;
    }

    private static function checkAndInitialize(): void
    {
        $clientConfig = new ClientConfig(); // todo
        self::initializeInternal($clientConfig);
    }

    private static function initializeInternal(ClientConfig $config): void
    {
        if (self::isEnabled()) {
            if (!self::$init) {
                self::$producer = DefaultMessageProducer::getInstance();
                self::$manager = DefaultMessageManager::getInstance();

                self::$init = true;
            }
        }
    }

    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    private function __construct()
    {
        
    }
}