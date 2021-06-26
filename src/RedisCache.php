<?php

namespace Soupmix\Cache;

use Psr\SimpleCache\CacheInterface;
use Redis;
use DateInterval;
use DateTime;

class RedisCache extends Common implements CacheInterface
{

    private $handler;

    public function __construct(Redis $handler)
    {
        if (defined('Redis::SERIALIZER_IGBINARY') && extension_loaded('igbinary')) {
            $handler->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
        }
        $this->handler = $handler;
    }

    public function get($key, $default = null)
    {
        $this->checkReservedCharacters($key);
        $value = $this->handler->get($key);
        return $value ?? $default;
    }

    public function set($key, $value, $ttl = null) : bool
    {
        $this->checkReservedCharacters($key);
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }
        $setTtl = (int) $ttl;
        if ($setTtl === 0) {
            return $this->handler->set($key, $value);
        }
        return $this->handler->setex($key, $ttl, $value);
    }


    public function delete($key) : bool
    {
        return (bool) $this->handler->del($key);
    }

    public function clear() : bool
    {
        return $this->handler->flushDB();
    }

    public function getMultiple($keys, $default = null) : array
    {
        $defaults = array_fill(0, count($keys), $default);
        foreach ($keys as $key) {
            $this->checkReservedCharacters($key);
        }
        return array_merge(array_combine($keys, $this->handler->mget($keys)), $defaults);
    }
    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null) : bool
    {
        foreach ($values as $key => $value) {
            $this->checkReservedCharacters($key);
        }
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }
        $setTtl = (int) $ttl;
        if ($setTtl === 0) {
            return $this->handler->mset($values);
        }
        $return = true;
        foreach ($values as $key => $value) {
            $return = $return && $this->set($key, $value, $setTtl);

        }
        return $return;
    }

    public function has($key)
    {
        $this->checkReservedCharacters($key);
        return (bool) $this->handler->exists($key);
    }

}
