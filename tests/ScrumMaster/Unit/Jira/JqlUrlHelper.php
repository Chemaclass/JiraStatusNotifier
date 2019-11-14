<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Tests\Unit\Jira;

trait JqlUrlHelper
{
    private function removeNewLines(string $query): string
    {
        return trim(preg_replace('/\s+/', ' ', $query));
    }
}
