<?php

namespace Orlion\CatAgentPhp\Message;

use Throwable;

interface MessageProducer
{
    public function createMessageId(): string;

    public function createRpcServerId(string $domain): string;

    public function logError(string $message, Throwable $cause = null): void;

    public function logErrorWithCategory(string $category, string $message, Throwable $cause = null): void;

    public function logEvent(string $type, string $name, string $status = '', string $nameValuePairs = ''): void;

    public function logHeartbeat(string $type, string $name, string $status, string $nameValuePairs): void;

    public function logMetric(string $type, string $name, string $nameValuePairs): void;

    public function newEvent(string $type, string $name): Event;

    public function newHeartbeat(string $type, string $name): Heartbeat;

    public function newMetric(string $type, string $name): Metric;

    public function newTrace(string $type, string $name): Trace;

    public function newTransaction(string $type, string $name): Transaction;
}