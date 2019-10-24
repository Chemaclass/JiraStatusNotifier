<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;

final class SlackMessage
{
    public static function fromJiraTickets(array $jiraTickets)
    {
        $result = '';

        /** @var JiraTicket $jiraTicket */
        foreach ($jiraTickets as $jiraTicket) {
            $result .= <<<TXT
The ticket "{$jiraTicket->title()}" ({$jiraTicket->key()}) is still in review since one day.
Assignee to {$jiraTicket->assignee()->displayName()} ({$jiraTicket->assignee()->name()}), please take of it!


TXT;
        }

        return $result;
    }
}
