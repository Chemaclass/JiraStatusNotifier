<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Channel\Email;

use Chemaclass\JiraStatusNotifier\Domain\Channel\Email\AddressGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Address;

final class AddressGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function for_jira_ticket_with_assignee(): void
    {
        $assignee = new Assignee('acountId_!@#$', 'Display Full Name');
        $jiraIdsToEmail = [$assignee->accountId() => 'any@email.com'];
        $ticket = $this->newJiraTicket($assignee);

        self::assertEquals(
            new Address('any@email.com', $assignee->displayName()),
            (new AddressGenerator($jiraIdsToEmail))->forJiraTicket($ticket)
        );
    }

    /**
     * @test
     */
    public function for_jira_ticket_without_assignee(): void
    {
        $jiraIdsToEmail = ['acountId_!@#$' => 'any@email.com'];
        $ticket = $this->newJiraTicket(Assignee::empty());

        self::assertNull((new AddressGenerator($jiraIdsToEmail))->forJiraTicket($ticket));
    }

    private function newJiraTicket(Assignee $assignee): JiraTicket
    {
        return new JiraTicket(
            'Ticket Title',
            'KEY-N',
            new TicketStatus('IN QA', new DateTimeImmutable()),
            $assignee
        );
    }
}
