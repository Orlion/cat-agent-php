<?php

namespace Orlion\CatAgentPhp\Tests;

use Orlion\CatAgentPhp\CatAgent;
use Orlion\CatAgentPhp\CatAgentContext;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Transaction;

require_once __DIR__ . '/../vendor/autoload.php';

class CatTest
{
    public function __construct()
    {
        CatAgent::init('www.cat-agent-sample.com', '127.0.0.1:2380');
    }

    public function send()
    {
        $transaction = CatAgent::newTransaction('URL', '/index/');

        CatAgent::logEvent('URL.Server', 'serverIp', Event::SUCCESS, ['ip' => 'serverIp']);

        sleep(1);
        $transaction->setStatus(Transaction::SUCCESS);
        $transaction->complete();

        $transaction = CatAgent::newTransaction('URL', 'http://www.test.com');

        $transaction1 = CatAgent::newTransaction('SQL', 'SELECT * FROM TEST');

        $event = CatAgent::newEvent('EventType', 'EventName');

        $event->addData(['k1' => 'v1']);
        $event->complete();
        sleep(2);
        $transaction1->setStatus('-1');
        $transaction1->complete();

        $transaction->setStatus(Transaction::SUCCESS);
        $transaction->complete();

        $ctx = new CatAgentContextImpl();
        CatAgent::logRemoteCallClient($ctx, 'api.callee.com');
        var_dump($ctx);
    }

    public function createMessageId()
    {
        $messageId = CatAgent::getProducer()->createMessageId();
        var_dump($messageId);
        $messageId = CatAgent::getProducer()->createMessageId();
        var_dump($messageId);
        $messageId = CatAgent::getProducer()->createMessageId();
        var_dump($messageId);
        $messageId = CatAgent::getProducer()->createMessageId();
        var_dump($messageId);
        $messageId = CatAgent::getProducer()->createMessageId();
        var_dump($messageId);
        $messageId = CatAgent::getProducer()->createMessageId();
        var_dump($messageId);
    }
}

class CatAgentContextImpl implements CatAgentContext
{
    private $map = [];

    public function addProperty(string $key, string $value): void
    {
        $this->map[$key] = $value;
    }

    public function getProperty(string $key): string
    {
        return $this->map[$key] ?? '';
    }
}

$catTest = new CatTest();
$catTest->send();