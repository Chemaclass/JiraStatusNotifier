<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira\ReadModel;

use DateTimeImmutable;

final class TicketStatus
{
    /** @var string */
    private $name;

    /** @var DateTimeImmutable */
    private $changeDate;

    public function __construct(string $name, DateTimeImmutable $changeDate)
    {
        $this->name = $name;
        $this->changeDate = $changeDate;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function changeDate(): DateTimeImmutable
    {
        return $this->changeDate;
    }
}
