<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\IgnoreStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

final class TicketIgnorer
{
    private IgnoreStrategy $ignoreStrategy;

    public static function byAssigneeKey(string...$assigneeKeys): self
    {
        return new self(new IgnoreByAssigneeKey(...$assigneeKeys));
    }

    private function __construct(IgnoreStrategy $ignoreStrategy)
    {
        $this->ignoreStrategy = $ignoreStrategy;
    }

    public function shouldIgnore(JiraTicket $ticket): bool
    {
        return $this->ignoreStrategy->shouldIgnore($ticket);
    }
}
