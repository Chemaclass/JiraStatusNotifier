<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class Board implements BoardInterface
{
    /** @var array */
    private $maxDaysInStatus;

    /** @var int */
    private $fallbackValue;

    public function __construct(array $maxDaysInStatus, int $fallbackValue = 1)
    {
        $this->maxDaysInStatus = $maxDaysInStatus;
        $this->fallbackValue = $fallbackValue;
    }

    public function maxDaysInStatus(): array
    {
        return $this->maxDaysInStatus;
    }

    public function getDaysForStatus(string $status): int
    {
        return $this->maxDaysInStatus[$status] ?? $this->fallbackValue;
    }
}
