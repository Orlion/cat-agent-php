<?php

namespace Orlion\CatAgentPhp;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Transaction;

class CatTest
{
    public function __construct()
    {
        
    }

    public function run()
    {
        $transaction = CatAgent::newTransaction('URL', 'http://www.test.com');

        CatAgent::logEvent('URL.Server', 'serverIp', Event::SUCCESS, "ip=serverIp");
        CatAgent::logMetricForCount("metric.key");
        CatAgent::logMetricForDuration("metric.key", 5);

        $transaction->setStatus(Transaction::SUCCESS);
        $transaction->complete();
    }
}

$catTest = new CatTest();
$catTest->run();