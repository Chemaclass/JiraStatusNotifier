<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\IgnoreStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

/** @psalm-immutable */
final class IgnoreByAssigneeKey implements IgnoreStrategyInterface
{
    /** @psalm-return list<string> */
    private array $assigneeKeys;

    public function __construct(string...$assigneeKeys)
    {
        $this->assigneeKeys = $assigneeKeys;
    }

    public function shouldIgnore(JiraTicket $ticket): bool
    {
        return in_array($ticket->assignee()->key(), $this->assigneeKeys);
    }
}
