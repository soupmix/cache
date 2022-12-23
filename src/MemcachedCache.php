<?php

declare(strict_types=1);

namespace Soupmix\Cache;

use DateInterval;
use DateTime;
use Memcached;
use Psr\SimpleCache\CacheInterface;

use function array_fill;
use function array_merge;
use function count;
use function defined;
use function extension_loaded;
use function in_array;
use function ini_set;
use function time;

class MemcachedCache extends Common implements CacheInterface
{
    public $handler;

    public function __construct(Memcached $handler)
    {
        $this->handler = $handler;
        if (! defined('Memcached::HAVE_IGBINARY') || ! extension_loaded('igbinary')) {
            return;
        }

        ini_set('memcached.serializer', 'igbinary');
    }

    public function get($key, $default = null)
    {
        $this->checkReservedCharacters($key);
        $value = $this->handler->get($key);

        return $value ?: $default;
    }

    public function set($key, $value, $ttl = null): bool
    {
        $this->checkReservedCharacters($key);
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }

        return $this->handler->set($key, $value, (int) $ttl);
    }

    public function delete($key): bool
    {
        $this->checkReservedCharacters($key);

        return $this->handler->delete($key);
    }

    public function clear(): bool
    {
        return $this->handler->flush();
    }

    public function getMultiple($keys, $default = null): array
    {
        $defaults = array_fill(0, count($keys), $default);
        foreach ($keys as $key) {
            $this->checkReservedCharacters($key);
        }

        return array_merge($this->handler->getMulti($keys), $defaults);
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->checkReservedCharacters($key);
        }

        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }

        return $this->handler->setMulti($values, (int) $ttl);
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->checkReservedCharacters($key);
        }

        $result = $this->handler->deleteMulti($keys);
        if (in_array(false, $result, true)) {
            return false;
        }

        return true;
    }

    public function has($key): bool
    {
        $this->checkReservedCharacters($key);
        $value = $this->handler->get($key);

        return $this->handler->getResultCode() !== Memcached::RES_NOTFOUND;
    }
}
