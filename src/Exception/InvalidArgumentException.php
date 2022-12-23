<?php

declare(strict_types=1);

namespace Soupmix\Cache\Exception;

use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

class InvalidArgumentException extends \InvalidArgumentException implements PsrInvalidArgumentException
{
}
