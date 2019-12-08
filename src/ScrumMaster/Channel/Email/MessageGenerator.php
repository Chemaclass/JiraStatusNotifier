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
        $assignee = $tickets[array_key_first($tickets)]->assignee();
        $text = $this->headerText($assignee);
        uksort($tickets, 'strnatcasecmp');

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

        return <<<TXT
<div class="ticket">
    <b>Jira Ticket</b>: <a href="{$url}">{$ticket->key()}</a> - <i>{$ticket->title()}</i> <br>
    <b>Current status</b>: <i>{$status->name()}</i> since {$daysDiff} {$dayWord}<br>
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

        return '<header>' . $salutation . ', please have a look:</header><hr>';
    }
}
