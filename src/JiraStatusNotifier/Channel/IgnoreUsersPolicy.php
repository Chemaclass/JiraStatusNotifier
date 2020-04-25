<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

/** @psalm-immutable */
final class IgnoreUsersPolicy
{
    /** @psalm-return list<string> */
    private array $usersToIgnore;

    public function __construct(string...$usersToIgnore)
    {
        $this->usersToIgnore = $usersToIgnore;
    }

    public function shouldIgnore(JiraTicket $ticket): bool
    {
        return in_array($ticket->assignee()->key(), $this->usersToIgnore);
    }
}
