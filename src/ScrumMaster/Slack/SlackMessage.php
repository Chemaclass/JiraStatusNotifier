<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;

final class SlackMessage
{
    public static function fromJiraTickets(array $jiraTickets): string
    {
        $result = '';

        foreach ($jiraTickets as $jiraTicket) {
            $result .= static::generateMessageFromTicket($jiraTicket) . PHP_EOL;
        }

        return $result;
    }

    private static function generateMessageFromTicket(JiraTicket $jiraTicket): string
    {
        return <<<TXT
The ticket "{$jiraTicket->title()}" ({$jiraTicket->key()}) is still {$jiraTicket->status()} since one day.
Assignee to {$jiraTicket->assignee()->displayName()} ({$jiraTicket->assignee()->name()}), please take of it!

TXT;
    }
}
