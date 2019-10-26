<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use DateTimeImmutable;

final class SlackMessage implements MessageGeneratorInterface
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

    public function forJiraTicket(JiraTicket $ticket, string $companyName): string
    {
        $assignee = $ticket->assignee();
        $status = $ticket->status();
        $daysDiff = $status->changeDate()->diff($this->timeToDiff)->days;
        $url = "https://{$companyName}.atlassian.net/browse/{$ticket->key()}";
        $dayWord = ($daysDiff > 1) ? 'days' : 'day';

        $text = static::headerText($assignee) . <<<TXT
*Ticket*: {$ticket->title()}[<{$url}|{$ticket->key()}>]
*Current status*: {$status->name()} since {$daysDiff} {$dayWord}
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
