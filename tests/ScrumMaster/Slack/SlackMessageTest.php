<?php

declare(strict_types=1);

namespace App\Tests\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use App\ScrumMaster\Jira\ReadModel\TicketStatus;
use App\ScrumMaster\Slack\SlackMessage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SlackMessageTest extends TestCase
{
    /** @test */
    public function renderMessageWithAssignee(): void
    {
        $expectedMessage = <<<TXT
Hey, Full Name (assignee.name), please have a look
*Ticket*: Ticket Title[<https://company-name.atlassian.net/browse/CST-KEY|CST-KEY>]
*Current status*: IN QA since 2 days
*Story Points*: 5

TXT;

        $jiraTicket = new JiraTicket(
            $title = 'Ticket Title',
            $key = 'CST-KEY',
            new TicketStatus('IN QA', new DateTimeImmutable("2019-10-22")),
            new Assignee(
                $name = 'assignee.name',
                $key = 'assignee-key',
                $emailAddress = 'person@companymail.com',
                $displayName = 'Full Name'
            ),
            $storyPoints = 5
        );

        $this->assertEquals($expectedMessage, SlackMessage::fromJiraTicket($jiraTicket, 'company-name'));
    }

    /** @test */
    public function renderMessageWithoutAssignee(): void
    {
        $expectedMessage = <<<TXT
Hey Team, please have a look
*Ticket*: Ticket Title[<https://company-name.atlassian.net/browse/CST-KEY|CST-KEY>]
*Current status*: IN QA since 2 days
*Story Points*: 5

TXT;

        $jiraTicket = new JiraTicket(
            $title = 'Ticket Title',
            $key = 'CST-KEY',
            new TicketStatus('IN QA', new DateTimeImmutable("2019-10-22")),
            new Assignee(
                $name = null,
                $key = null,
                $emailAddress = null,
                $displayName = null
            ),
            $storyPoints = 5
        );

        $this->assertEquals($expectedMessage, SlackMessage::fromJiraTicket($jiraTicket, 'company-name'));
    }
}
