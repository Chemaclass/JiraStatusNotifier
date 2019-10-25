<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

interface BoardInterface
{
    public function maxDaysInStatus(string $status): int;
}
