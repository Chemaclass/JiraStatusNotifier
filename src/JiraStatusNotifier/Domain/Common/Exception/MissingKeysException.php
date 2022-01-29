<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Common\Exception;

/** @psalm-immutable */
final class MissingKeysException extends \Exception
{
    public function __construct(array $missingKeys)
    {
        parent::__construct(implode(', ', $missingKeys) . ' keys are mandatory but missing!');
    }
}
