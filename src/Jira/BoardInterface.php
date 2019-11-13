<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

interface BoardInterface
{
    /** @return array<string, int> */
    public function maxDaysInStatus(): array;

    public function getDaysForStatus(string $status): int;
}
