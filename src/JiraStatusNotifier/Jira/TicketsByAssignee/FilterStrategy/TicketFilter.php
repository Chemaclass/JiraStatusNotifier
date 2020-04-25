<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\FilterStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

final class TicketFilter
{
    private FilterStrategy $filterStrategy;

    public static function notWithAssigneeKeys(string...$assigneeKeys): self
    {
        return new self(new FilterNotWithAssigneeKeys(...$assigneeKeys));
    }

    private function __construct(FilterStrategy $ignoreStrategy)
    {
        $this->filterStrategy = $ignoreStrategy;
    }

    public function shouldIgnore(JiraTicket $ticket): bool
    {
        return !$this->filterStrategy->isValidTicket($ticket);
    }
}
