<?php

declare(strict_types=1);

namespace Soupmix\Cache;

use DateInterval;
use DateTime;
use Soupmix\Cache\Exception\InvalidArgumentException;

use function is_string;
use function sprintf;
use function str_contains;
use function time;

abstract class Common
{
    private const PSR16_RESERVED_CHARACTERS = ['{', '}', '(', ')', '/', '@', ':'];

    protected function checkReservedCharacters($key): void
    {
        if (! is_string($key)) {
            $message = sprintf('key %s is not a string.', $key);

            throw new InvalidArgumentException($message);
        }

        foreach (self::PSR16_RESERVED_CHARACTERS as $needle) {
            if (str_contains($key, $needle)) {
                $message = sprintf('%s string is not a legal value.', $key);

                throw new InvalidArgumentException($message);
            }
        }
    }

    public function getMultiple($keys, $default = null): array
    {
        $values = [];
        foreach ($keys as $key) {
            $this->checkReservedCharacters($key);
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        $isEverythingOK = true;
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime('now'))->add($ttl)->getTimeStamp() - time();
        }

        if ($ttl !== null) {
            $ttl = ((int) $ttl) + time();
        }

        foreach ($values as $key => $value) {
            $this->checkReservedCharacters($key);
            $result = $this->set($key, $value, $ttl);
            if ($result) {
                continue;
            }

            $isEverythingOK = false;
        }

        return $isEverythingOK;
    }

    public function deleteMultiple($keys): bool
    {
        $isEverythingOK = true;
        foreach ($keys as $key) {
            $this->checkReservedCharacters($key);
            $result = $this->delete($key);
            if ($result) {
                continue;
            }

            $isEverythingOK = false;
        }

        return $isEverythingOK;
    }
}
