<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

final class Board implements BoardInterface
{
    /** @var array <string,int> */
    private $maxDaysInStatus;

    /** @var int */
    private $fallbackValue;

    public function __construct(array $maxDaysInStatus, int $fallbackValue = 1)
    {
        $this->maxDaysInStatus = $maxDaysInStatus;
        $this->fallbackValue = $fallbackValue;
    }

    public function getDaysForStatus(string $status): int
    {
        return $this->maxDaysInStatus()[$status] ?? $this->fallbackValue;
    }

    public function maxDaysInStatus(): array
    {
        return $this->maxDaysInStatus;
    }
}
