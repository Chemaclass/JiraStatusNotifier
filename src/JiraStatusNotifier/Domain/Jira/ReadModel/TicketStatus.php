<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel;

use DateTimeImmutable;

final class TicketStatus
{
    private string $name;

    private DateTimeImmutable $changeDate;

    public function __construct(string $name, DateTimeImmutable $changeDate)
    {
        $this->name = $name;
        $this->changeDate = $changeDate;
    }

    public static function empty(): self
    {
        return new self('', new DateTimeImmutable());
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
