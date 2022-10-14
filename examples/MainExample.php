<?php

namespace Examples;

use Orlion\CatAgentPhp\CatAgent;
use Orlion\CatAgentPhp\CatAgentConstants;
use Orlion\CatAgentPhp\CatAgentContext;

class MainExample
{
    public function __construct()
    {
        CatAgent::init('demo.cat-agent.com', 'unix:///var/run/cat-agent.sock');
    }

    public function run()
    {
        echo "RemoteCallExample run\n";
        $rootTrans = CatAgent::newTransaction(CatAgentConstants::TYPE_URL, '/index.php');

        $ctx = new CatAgentContextImpl();
        $ctx->addProperty(CatAgentContext::CHILD, $_SERVER['HTTP__CATCHILDMESSAGEID'] ?? '');
        $ctx->addProperty(CatAgentContext::ROOT, $_SERVER['HTTP__CATROOTMESSAGEID'] ?? '');
        $ctx->addProperty(CatAgentContext::PARENT, $_SERVER['HTTP__CATPARENTMESSAGEID'] ?? '');
        CatAgent::logRemoteCallServer($ctx);

        $this->execRedisCmd('article_cache_redis', 'get articles');
        $articles = $this->remoteCall('http://article-api.cat-agent-sample.com/articles.php', ['page' => 1, 'pageSize' => 10]);

        $this->execRedisCmd('news_cache_redis', 'get articles');
        $news = $this->remoteCall('http://news-api.cat-agent-sample.com/news.php', ['page' => 1, 'pageSize' => 10]);

        $this->execRedisCmd('ad_cache_redis', 'get articles');
        $ads = $this->remoteCall('http://ad-api.cat-agent-sample.com/ads.php', ['page' => 1, 'pageSize' => 10]);

        $user = $this->execSql('CatAgentDB', 'select * from user where user_id=?', [rand(1, 10000000)]);

        $rootTrans->complete();
        echo "RemoteCallExample end\n";
    }

    protected function remoteCall(string $url, array $params)
    {
        $domain = parse_url($url, PHP_URL_HOST);

        $ctx = new CatAgentContextImpl();
        CatAgent::logRemoteCallClient($ctx, $domain);

        $callTrans = CatAgent::newTransaction(CatAgentConstants::TYPE_CALL, $url);

        $callTrans->setData($params);

        $headers = [
            CatAgentContext::ROOT => $ctx->getProperty(CatAgentContext::ROOT),
            CatAgentContext::PARENT => $ctx->getProperty(CatAgentContext::PARENT),
            CatAgentContext::CHILD => $ctx->getProperty(CatAgentContext::CHILD),
        ];

        list($result, $success) = $this->doRequset($url, $params, $headers);
        if (!$success) {
            $callTrans->setStatus('failed');
        }

        $callTrans->complete();
    }

    protected function doRequset(string $url, array $params, array $headers): array
    {
        usleep(100 * rand(10, 1500));
        $result = '{"k":"v"}';
        $success = rand(1, 10) > 7;
        return [$result, $success];
    }

    protected function execSql(string $database, string $sql, array $bindParams): array
    {
        $sqlTrans = CatAgent::newTransaction(CatAgentConstants::TYPE_SQL, $database);

        CatAgent::logEvent('SQL.name', $sql);

        usleep(100 * rand(10, 1500));

        if (rand(0, 10) > 7) {
            $sqlTrans->setStatus('sql error msg');
        }

        $sqlTrans->complete();

        return ['k' => 'v'];
    }

    protected function execRedisCmd(string $redis, string $cmd)
    {
        $sqlTrans = CatAgent::newTransaction(CatAgentConstants::TYPE_CACHE_PREFIX . 'Redis', $redis);

        CatAgent::logEvent('Redis.cmd', $cmd);

        usleep(100 * rand(5, 100));

        if (rand(0, 10) > 7) {
            $sqlTrans->setStatus('redis error msg');
        }

        $sqlTrans->complete();
    }
}