<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira;

/** @psalm-immutable */
final class Board implements BoardInterface
{
    /** @var array<string, int> */
    private array $maxDaysInStatus;

    public function __construct(array $maxDaysInStatus)
    {
        $this->maxDaysInStatus = $maxDaysInStatus;
    }

    public function maxDaysInStatus(): array
    {
        return $this->maxDaysInStatus;
    }

    public function getDaysForStatus(string $status): int
    {
        return $this->maxDaysInStatus[$status] ?? 0;
    }
}
