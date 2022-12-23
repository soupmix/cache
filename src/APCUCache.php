<?php

declare(strict_types=1);

namespace Soupmix\Cache;

use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;

use function apcu_clear_cache;
use function apcu_delete;
use function apcu_exists;
use function apcu_fetch;
use function apcu_store;
use function time;

class APCUCache extends Common implements CacheInterface
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
    public function set($key, $value, $ttl = null): bool
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
    public function delete($key): bool
    {
        $this->checkReservedCharacters($key);

        return (bool) apcu_delete($key);
    }

    public function clear(): bool
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
