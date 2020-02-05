<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

final class Board implements BoardInterface
{
    public const FALLBACK_VALUE_DEFAULT = 1;

    /** @var array<string,int> */
    private $maxDaysInStatus;

    private int $fallbackValue;

    public function __construct(array $maxDaysInStatus, int $fallbackValue = self::FALLBACK_VALUE_DEFAULT)
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
