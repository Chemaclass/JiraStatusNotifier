<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\IgnoreStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

final class IgnoreStrategy
{
    private IgnoreStrategyInterface $ignoreStrategy;

    public static function byAssigneeKey(string...$assigneeKeys): self
    {
        return new self(new IgnoreByAssigneeKey(...$assigneeKeys));
    }

    private function __construct(IgnoreStrategyInterface $ignoreStrategy)
    {
        $this->ignoreStrategy = $ignoreStrategy;
    }

    public function shouldIgnoreTicket(JiraTicket $ticket): bool
    {
        return $this->ignoreStrategy->shouldIgnore($ticket);
    }
}
