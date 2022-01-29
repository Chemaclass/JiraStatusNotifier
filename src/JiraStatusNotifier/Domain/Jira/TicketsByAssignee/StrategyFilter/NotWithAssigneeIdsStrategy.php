<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;

/**@psalm-immutable */
final class NotWithAssigneeIdsStrategy implements StrategyFilter
{
    /** @var list<string> */
    private array $assigneeIds;

    public function __construct(string ...$assigneeIds)
    {
        $this->assigneeIds = $assigneeIds;
    }

    public function isValidTicket(JiraTicket $ticket): bool
    {
        return !in_array($ticket->assignee()->accountId(), $this->assigneeIds, true);
    }
}
