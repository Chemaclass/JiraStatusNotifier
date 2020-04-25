<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira\TicketsByAssignee;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\TicketStatus;
use Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\TicketsByAssignee;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TicketsByAssigneeTest extends TestCase
{
    /** @test */
    public function add(): void
    {
        $ticket1 = $this->newJiraTicket('assigneeKey1', 'KEY-1');
        $ticket2 = $this->newJiraTicket('assigneeKey1', 'KEY-2');
        $ticket3 = $this->newJiraTicket('assigneeKey2', 'KEY-3');

        $ticketsByAssignee = (new TicketsByAssignee())
            ->add($ticket1)
            ->add($ticket2)
            ->add($ticket3);

        self::assertEquals([
            'assigneeKey1' => [$ticket1, $ticket2],
            'assigneeKey2' => [$ticket3],
        ], $ticketsByAssignee->list());
    }

    private function newJiraTicket(string $assigneeKey, string $ticketKey): JiraTicket
    {
        return new JiraTicket(
            'The title',
            $ticketKey,
            new TicketStatus('In Progress', new DateTimeImmutable()),
            new Assignee(
                'assignee.name',
                $assigneeKey,
                'Full Name',
                'any@example.com'
            )
        );
    }
}
