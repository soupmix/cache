<?php
namespace Soupmix\Cache\Tests;

use Soupmix\Cache\Exception\InvalidArgumentException;
use Soupmix;
use DateInterval;
use Soupmix\Cache\APCUCache;
use Psr\SimpleCache\CacheInterface;

class APCUCacheTest  extends AbstractTestCases
{
    /**
     * @var $client CacheInterface
     */
    protected $client;

    protected function setUp() : void
    {
        $this->client = new APCUCache();
        $this->client->clear();
    }

}
