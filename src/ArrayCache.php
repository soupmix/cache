<?php

namespace Soupmix\Cache;

use Psr\SimpleCache\CacheInterface;
use DateInterval;
use DateTime;

class ArrayCache extends Common implements CacheInterface
{
    private $bucket = [];

    public function get($key, $default = null)
    {
        $this->checkReservedCharacters($key);
        if (!$this->has($key)) {
            return $default;
        }
        return $this->bucket[$key][0];
    }

    public function set($key, $value, $ttl = null) : bool
    {
        $this->checkReservedCharacters($key);
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }
        if ($ttl !== null) {
            $ttl = ((int) $ttl) + time();
        }
        $this->bucket[$key] = [$value, $ttl ?? false];
        return true;
    }

    public function delete($key) : bool
    {
        $this->checkReservedCharacters($key);
        if (array_key_exists($key, $this->bucket)) {
            unset($this->bucket[$key]);
            return true;
        }
        return false;
    }

    public function clear() : bool
    {
        $this->bucket = [];
        return true;
    }

    public function has($key) : bool
    {
        $this->checkReservedCharacters($key);
        if (!array_key_exists($key, $this->bucket)) {
            return false;
        }
        if ($this->bucket[$key][1] !== false && $this->bucket[$key][1] <= time()) {
            $this->delete($key);
            return false;
        }
        return true;
    }


}
