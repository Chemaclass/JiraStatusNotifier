<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira\TicketsByAssignee;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\TicketStatus;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\TicketsByAssignee;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TicketsByAssigneeTest extends TestCase
{
    /**
     * @test
     */
    public function add(): void
    {
        $ticket1 = $this->newJiraTicket('assigneeId1', 'KEY-1');
        $ticket2 = $this->newJiraTicket('assigneeId1', 'KEY-2');
        $ticket3 = $this->newJiraTicket('assigneeId2', 'KEY-3');

        $ticketsByAssignee = (new TicketsByAssignee())
            ->add($ticket1)
            ->add($ticket2)
            ->add($ticket3);

        self::assertEquals([
            'assigneeId1' => [$ticket1, $ticket2],
            'assigneeId2' => [$ticket3],
        ], $ticketsByAssignee->list());
    }

    private function newJiraTicket(string $assigneeId, string $ticketKey): JiraTicket
    {
        return new JiraTicket(
            'The title',
            $ticketKey,
            new TicketStatus('In Progress', new DateTimeImmutable()),
            new Assignee($assigneeId, 'Display Full Name')
        );
    }
}
