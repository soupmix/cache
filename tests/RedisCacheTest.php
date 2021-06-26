<?php
namespace Soupmix\Cache\Tests;

use Soupmix\Cache as c;
use Redis;

class RedisCacheTest extends AbstractTestCases
{

    protected $client = null;

    protected $redisConfig = [
        'host'   => '127.0.0.1',
        'persistent' => null,
        'bucket' => 'default',
        'dbIndex' => 0,
        'port' => 6379,
        'timeout' => 2.5,
        'persistentId' => 'main',
        'reconnectAttempt' => 100
    ];

    protected function setUp() : void
    {
        $handler = new Redis();
        $handler->connect(
            $this->redisConfig['host'],
            $this->redisConfig['port'],
            $this->redisConfig['timeout'],
            null,
            $this->redisConfig['reconnectAttempt']
        );
        $handler->select($this->redisConfig['dbIndex']);
        $this->client = new c\RedisCache($handler);
        $this->client->clear();
    }
}
