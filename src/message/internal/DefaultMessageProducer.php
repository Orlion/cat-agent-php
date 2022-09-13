<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageProducer;
use Orlion\CatAgentPhp\Message\Metric;
use Orlion\CatAgentPhp\Message\Transaction;
use Throwable;

class DefaultMessageProducer implements MessageProducer
{
    private $manager;
    private static $instance;

    private function __construct()
    {
        $this->manager = DefaultMessageManager::getInstance();
    }

    public static function getInstance(): DefaultMessageProducer
    {
        if (is_null(self::$instance)) {
            self::$instance = new DefaultMessageProducer();
        }
        
        return self::$instance;
    }

    public function logError(Throwable $cause, string $message = ''): void
    {

    }

    public function logErrorWithCategory(string $category, Throwable $cause, string $message = ''): void
    {
        
    }

    public function logEvent(string $type, string $name, string $status = '', string $nameValuePairs = ''): void
    {
        
    }

    public function logMetric(string $type, string $name, string $nameValuePairs): void
    {
        
    }

    public function newEvent(string $type, string $name): Event
    {
        return new DefaultEvent($type, $name);
    }

    public function newMetric(string $type, string $name): Metric
    {
        
    }

    public function newTransaction(string $type, string $name): Transaction
    {
        $transaction = new DefaultTransaction($type, $name);

        $this->manager->start($transaction);
        return $transaction;
    }
}

