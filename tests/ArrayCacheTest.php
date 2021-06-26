<?php
namespace Soupmix\Cache\Tests;

use Soupmix\Cache\Exception\InvalidArgumentException;
use Soupmix;
use DateInterval;
use Soupmix\Cache\ArrayCache;
use Psr\SimpleCache\CacheInterface;

class ArrayCacheTest  extends AbstractTestCases
{
    /**
     * @var $client CacheInterface
     */
    protected $client;

    protected function setUp() : void
    {
        $this->client = new ArrayCache();
        $this->client->clear();
    }


}
