<?php

namespace Examples;

use Orlion\CatAgentPhp\CatAgent;
use Orlion\CatAgentPhp\CatAgentConstants;
use Orlion\CatAgentPhp\CatAgentContext;

class RemoteCallExample
{
    public function __construct()
    {
        CatAgent::init('www.cat-agent-sample.com', '127.0.0.1:2380');
    }

    public function run()
    {
        echo "RemoteCallExample run";
        $callerTrans = CatAgent::newTransaction(CatAgentConstants::TYPE_URL, '/caller.php');

        $this->remoteCall('http://api.cat-agent-sample.com/callee.php', ['age' => 1, 'name' => 'test']);

        $callerTrans->complete();
        echo "RemoteCallExample end";
    }

    protected function remoteCall(string $url, array $params)
    {
        $domain = parse_url($url, PHP_URL_HOST);

        $ctx = new CatAgentContextImpl();
        CatAgent::logRemoteCallClient($ctx, $domain);

        $callTrans = CatAgent::newTransaction('Call', $url);

        $callTrans->setData($params);

        $params = array_merge($params, [
            CatAgentContext::ROOT => $ctx->getProperty(CatAgentContext::ROOT),
            CatAgentContext::PARENT => $ctx->getProperty(CatAgentContext::PARENT),
            CatAgentContext::CHILD => $ctx->getProperty(CatAgentContext::CHILD),
        ]);

        list($result, $status) = $this->doRequset($url, $params);
        if (!$status) {
            $callTrans->setStatus('failed');
        }

        $callTrans->complete();
    }

    protected function doRequset(string $url, array $params): array
    {
        return $this->server($url, $params);
    }

    protected function server(string $url, array $params): array
    {
        $calleeTrans = CatAgent::newTransaction(CatAgentConstants::TYPE_URL, parse_url($url, PHP_URL_PATH));

        $ctx = new CatAgentContextImpl();
        $ctx->addProperty(CatAgentContext::ROOT, $params[CatAgentContext::ROOT]);
        $ctx->addProperty(CatAgentContext::PARENT, $params[CatAgentContext::PARENT]);
        $ctx->addProperty(CatAgentContext::CHILD, $params[CatAgentContext::CHILD]);
        CatAgent::logRemoteCallServer($ctx);

        $result = $this->callee();
        
        $calleeTrans->complete();

        return [$result, true];
    }

    protected function callee(): array
    {
        sleep(1);
        return ['k' => 'v'];
    }
}