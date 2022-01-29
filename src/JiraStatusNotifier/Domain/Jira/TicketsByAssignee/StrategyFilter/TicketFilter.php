<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;

final class TicketFilter
{
    private StrategyFilter $strategyFilter;

    public static function notWithAssigneeKeys(string ...$assigneeKeys): self
    {
        return new self(new NotWithAssigneeIdsStrategy(...$assigneeKeys));
    }

    private function __construct(StrategyFilter $strategyFilter)
    {
        $this->strategyFilter = $strategyFilter;
    }

    public function shouldIgnore(JiraTicket $ticket): bool
    {
        return !$this->strategyFilter->isValidTicket($ticket);
    }
}
