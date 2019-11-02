<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierOutput;
use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use App\ScrumMaster\Jira\ReadModel\TicketStatus;
use App\ScrumMaster\Slack\SlackNotifierResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SlackNotifierOutputTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = new SlackNotifierResult();
        $result->addTicketWithResponseCode($this->newTicket('KEY-1'), 100);
        $result->addTicketWithResponseCode($this->newTicket('KEY-2'), 200);
        $result->addTicketWithResponseCode($this->newTicket('KEY-3'), 300);
        $result->addTicketWithResponseCode($this->newTicket('KEY-4'), 400);
        $result->addTicketWithResponseCode($this->newTicket('KEY-5'), 500);

        $output = new InMemoryOutput();
        (new SlackNotifierOutput($output))->write($result);

        $this->assertContains('Total notifications: 5 (KEY-1, KEY-2, KEY-3, KEY-4, KEY-5)', $output->lines());
        $this->assertContains('Total successful notifications sent: 1', $output->lines());
        $this->assertContains('Total failed notifications sent: 4', $output->lines());
    }

    private function newTicket(string $key): JiraTicket
    {
        return new JiraTicket(
            'Ticket Title',
            $key,
            new TicketStatus('IN QA', new DateTimeImmutable()),
            new Assignee(
                'assignee.name',
                'assignee-key',
                'Display Name'
            ),
            $storyPoints = 5
        );
    }
}
