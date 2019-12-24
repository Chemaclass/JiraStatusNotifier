<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Common\Exception;

final class MissingKeysException extends \Exception
{

    public function __construct(array $missingKeys)
    {
        parent::__construct(implode(', ', $missingKeys) . ' keys are mandatory but missing!');
    }
}
