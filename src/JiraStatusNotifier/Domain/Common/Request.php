<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Common;

interface Request
{
    public const HTTP_OK = 200;

    public const HTTP_BAD_REQUEST = 400;
}
