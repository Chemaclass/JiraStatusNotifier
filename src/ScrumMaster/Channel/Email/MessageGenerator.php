<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use DateTimeImmutable;

final class MessageGenerator implements MessageGeneratorInterface
{
    /** @var DateTimeImmutable */
    private $timeToDiff;

    public static function withTimeToDiff(DateTimeImmutable $timeToDiff): self
    {
        return new self($timeToDiff);
    }

    private function __construct(DateTimeImmutable $timeToDiff)
    {
        $this->timeToDiff = $timeToDiff;
    }

    public function forJiraTickets(array $tickets, string $companyName): string
    {
        $ticket = $tickets[0];

        $assignee = $ticket->assignee();
        $text = $this->headerText($assignee);

        foreach ($tickets as $ticket) {
            $text .= $this->textForTicket($ticket, $companyName);
        }

        return $text;
    }

    private function textForTicket(JiraTicket $ticket, string $companyName): string
    {
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff($this->timeToDiff)->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';
        $assignee = $ticket->assignee() ? $ticket->assignee()->displayName() : 'None';

        return <<<TXT
<div class="ticket">
    <b>Ticket</b>: {$ticket->title()} <a href="{$url}">{$ticket->key()}</a><br>
    <b>Assignee</b>: {$assignee}<br>
    <b>Current status</b>: {$status->name()} since {$daysDiff} {$dayWord}<br>
    <b>Story Points</b>: {$ticket->storyPoints()}<br>
</div>
<hr>
TXT;
    }

    private function headerText(Assignee $assignee): string
    {
        if ($assignee->key()) {
            $salutation = "Hey, {$assignee->displayName()} ({$assignee->name()})";
        } else {
            $salutation = 'Hey Team';
        }

        return '<div class="header">' . $salutation . ', please have a look:</div><hr>';
    }
}
