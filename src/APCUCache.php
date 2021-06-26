<?php

namespace Soupmix\Cache;

use Psr\SimpleCache\CacheInterface;
use DateInterval;
use DateTime;

use function apcu_fetch;
use function apcu_store;
use function apcu_delete;
use function apcu_clear_cache;
use function apcu_dec;
use function apcu_inc;

class APCUCache  extends Common  implements CacheInterface
{
    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->checkReservedCharacters($key);
        $value = apcu_fetch($key);
        return $value ?: $default;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null) : bool
    {
        $this->checkReservedCharacters($key);
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }
        return apcu_store($key, $value, (int) $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key) : bool
    {
        $this->checkReservedCharacters($key);
        return (bool) apcu_delete($key);
    }

    /**
     * {@inheritDoc}
     */
    public function clear() : bool
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        $this->checkReservedCharacters($key);
        return apcu_exists($key);
    }

}
