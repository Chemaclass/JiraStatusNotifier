<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

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
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff($this->timeToDiff)->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';

        return $this->headerText($assignee) . <<<TXT
*Ticket*: {$ticket->title()}[<{$url}|{$ticket->key()}>]
*Current status*: {$status->name()} since {$daysDiff} {$dayWord}
*Story Points*: {$ticket->storyPoints()}

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
