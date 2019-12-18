<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel\Email;

use Chemaclass\ScrumMaster\Channel\Email\AddressGenerator;
use Chemaclass\ScrumMaster\Channel\Email\ByPassEmail;
use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use Chemaclass\ScrumMaster\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Address;

final class AddressGeneratorTest extends TestCase
{
    /** @test */
    public function byDefaultGoesToAssignee(): void
    {
        $jiraTicket = $this->newJiraTicket();
        $assignee = $jiraTicket->assignee();

        self::assertEquals([
            new Address($assignee->email(), $assignee->displayName()),
        ], (new AddressGenerator())->forJiraTicket($jiraTicket));
    }

    /** @test */
    public function sendCopyTo(): void
    {
        $jiraTicket = $this->newJiraTicket();
        $assignee = $jiraTicket->assignee();
        $generator = new AddressGenerator((new ByPassEmail())->setSendCopyTo('copy@to.me'));

        self::assertEquals([
            new Address($assignee->email(), $assignee->displayName()),
            new Address('copy@to.me', $assignee->displayName()),
        ], $generator->forJiraTicket($jiraTicket));
    }

    /** @test */
    public function sendCopyToWithoutAssignee(): void
    {
        $jiraTicket = $this->newJiraTicket();
        $assignee = $jiraTicket->assignee();

        $generator = new AddressGenerator((new ByPassEmail())
            ->setSendCopyTo('copy@to.me')
            ->setSendEmailsToAssignee(false));

        self::assertEquals([
            new Address('copy@to.me', $assignee->displayName()),
        ], $generator->forJiraTicket($jiraTicket));
    }

    private function newJiraTicket(): JiraTicket
    {
        return new JiraTicket(
            $title = 'Ticket Title',
            $key = 'CST-KEY',
            new TicketStatus('IN QA', new DateTimeImmutable()),
            new Assignee(
                $name = 'assignee.name',
                $key = 'assignee-key',
                $displayName = 'Full Name',
                $email = 'any@example.com'
            ),
            $storyPoints = 5
        );
    }
}
