<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\FilterStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

/** @psalm-immutable */
final class FilterByAssigneeKey implements FilterStrategy
{
    /** @psalm-return list<string> */
    private array $assigneeKeys;

    public function __construct(string...$assigneeKeys)
    {
        $this->assigneeKeys = $assigneeKeys;
    }

    public function isValidTicket(JiraTicket $ticket): bool
    {
        return !in_array($ticket->assignee()->key(), $this->assigneeKeys);
    }
}