<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

/** @psalm-immutable */
final class NotWithAssigneeIdsStrategy implements StrategyFilter
{
    /** @psalm-return list<string> */
    private array $assigneeIds;

    public function __construct(string...$assigneeIds)
    {
        $this->assigneeIds = $assigneeIds;
    }

    public function isValidTicket(JiraTicket $ticket): bool
    {
        return !in_array($ticket->assignee()->accountId(), $this->assigneeIds);
    }
}
