<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;

final class SlackMessage
{
    public static function fromJiraTicket(JiraTicket $ticket, string $companyName): string
    {
        $assignee = $ticket->assignee();
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff(new \DateTimeImmutable())->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';

        $text = static::headerText($assignee);

        $text .= <<<TXT
*Ticket*: {$ticket->title()}[<{$url}|{$ticket->key()}>]
*Current status*: {$status->name()} since {$daysDiff} $dayWord
*Story Points*: {$ticket->storyPoints()}

TXT;

        return $text;
    }

    private static function headerText(Assignee $assignee): string
    {
        if ($assignee->name()) {
            return "Hey, {$assignee->displayName()} ({$assignee->name()}), please have a look" . PHP_EOL;
        }

        return 'Hey Team, please have a look' . PHP_EOL;
    }
}
