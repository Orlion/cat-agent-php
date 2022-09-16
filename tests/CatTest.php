<?php

namespace Orlion\CatAgentPhp\Tests;

use Orlion\CatAgentPhp\CatAgent;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Transaction;

require_once __DIR__ . '/../vendor/autoload.php';

class CatTest
{
    public function __construct()
    {
        
    }

    public function run()
    {
        CatAgent::init('php-client-test', '127.0.0.1');

        $transaction = CatAgent::newTransaction('URL', 'http://www.test.com');

        CatAgent::logEvent('URL.Server', 'serverIp', Event::SUCCESS, "ip=serverIp");

        $transaction->setStatus(Transaction::SUCCESS);
        $transaction->complete();
    }
}

$catTest = new CatTest();
$catTest->run();