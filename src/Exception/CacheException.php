<?php
declare(strict_types=1);

namespace Soupmix\Cache\Exception;

use Psr\SimpleCache\CacheException as PsrCacheException;
class CacheException extends \InvalidArgumentException implements PsrCacheException
{

}