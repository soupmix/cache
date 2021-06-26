<?php
namespace Soupmix\Cache\Tests;

use Soupmix\Cache as c;
Use Memcached;

class MemcachedCacheTest extends AbstractTestCases
{

    protected function setUp() : void
    {
        $config = [
            'bucket' => 'test',
            'hosts' => ['127.0.0.1'],
        ];
        $handler = new Memcached($config['bucket']);
        $handler->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $handler->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
        if (!count($handler->getServerList())) {
            $hosts = [];
            foreach ($config['hosts'] as $host) {
                $hosts[] = [$host, 11211];
            }
            $handler->addServers($hosts);
        }
        $this->client = new c\MemcachedCache($handler);
        $this->client->clear();
    }


}