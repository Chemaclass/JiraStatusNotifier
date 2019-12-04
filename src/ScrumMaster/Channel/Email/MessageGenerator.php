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
        $header = $this->headerText($assignee);
        $text = '';

        foreach ($tickets as $ticket) {
            $text .= $this->textForTicket($ticket, $companyName);
        }

        return $header . $text;
    }

    private function textForTicket(JiraTicket $ticket, string $companyName): string
    {
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff($this->timeToDiff)->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';

        return <<<TXT
<b>Ticket</b>: {$ticket->title()} <a href="{$url}">{$ticket->key()}</a><br>
<b>Current status</b>: {$status->name()} since {$daysDiff} {$dayWord}<br>
<b>Story Points<b>: {$ticket->storyPoints()}<br>
TXT;
    }

    private function headerText(Assignee $assignee): string
    {
        if ($assignee->key()) {
            return "Hey, {$assignee->displayName()} ({$assignee->name()}), please have a look <br>";
        }

        return 'Hey Team, please have a look <br>';
    }
}
