<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use DateTimeImmutable;

final class MessageGenerator implements MessageGeneratorInterface
{
    private DateTimeImmutable $now;

    public static function beingNow(DateTimeImmutable $now): self
    {
        return new self($now);
    }

    private function __construct(DateTimeImmutable $now)
    {
        $this->now = $now;
    }

    public function forJiraTickets(array $tickets, string $companyName): string
    {
        $allTickets = '';

        foreach ($tickets as $ticket) {
            $allTickets .= $this->forOneJiraTicket($ticket, $companyName);
        }

        $ticket = $tickets[array_key_first($tickets)];

        return $this->headerText($ticket->assignee()) . $allTickets;
    }

    public function forOneJiraTicket(JiraTicket $ticket, string $companyName): string
    {
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff($this->now)->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';

        return <<<TXT
> [<{$url}|{$ticket->key()}>] {$ticket->title()}
*Current status*: {$status->name()} since {$daysDiff} {$dayWord} | *Story Points*: {$ticket->storyPoints()}

TXT;
    }

    private function headerText(Assignee $assignee): string
    {
        if ($assignee->key()) {
            return "Hey, {$assignee->displayName()} ({$assignee->name()}), please have a look" . PHP_EOL;
        }

        return 'Hey Team, please have a look' . PHP_EOL;
    }
}
