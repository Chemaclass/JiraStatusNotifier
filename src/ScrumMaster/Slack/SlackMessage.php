<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;

final class SlackMessage
{
    public static function fromJiraTickets(array $jiraTickets, ?string $companyName = null): string
    {
        $result = '';

        foreach ($jiraTickets as $jiraTicket) {
            $result .= static::generateMessageFromTicket($jiraTicket, $companyName) . PHP_EOL;
        }

        return $result;
    }

    private static function generateMessageFromTicket(JiraTicket $ticket, ?string $companyName = null): string
    {
        $assignee = $ticket->assignee();

        $text = <<<TXT
The ticket "{$ticket->title()}" ({$ticket->key()})[{$ticket->storyPoints()}SP] is still {$ticket->status()} since one day.
Assignee to {$assignee->displayName()} ({$assignee->name()}), please take of it!

TXT;
        if ($companyName) {
            $text .= "URL: https://{$companyName}.atlassian.net/browse/{$ticket->key()}" . PHP_EOL;
        }

        return $text;
    }
}
