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

    private static function generateMessageFromTicket(JiraTicket $ticket, string $companyName): string
    {
        $assignee = $ticket->assignee();
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff(new \DateTimeImmutable())->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';

        return <<<TXT
{$assignee->displayName()} ({$assignee->name()}), please take care of your work!
*Ticket*: {$ticket->title()}[<{$url}|{$ticket->key()}>]
*Current status*: *{$status->name()}* since *{$daysDiff} $dayWord*
*Story Points*: {$ticket->storyPoints()}

TXT;
    }
}
