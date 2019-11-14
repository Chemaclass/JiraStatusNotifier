<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Tests\Unit\Slack;

use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use Chemaclass\ScrumMaster\Jira\ReadModel\TicketStatus;
use Chemaclass\ScrumMaster\Slack\SlackMessage;
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
*Current status*: IN QA since 1 day
*Story Points*: 5

TXT;
        $statusDateChange = new DateTimeImmutable();

        $jiraTicket = new JiraTicket(
            $title = 'Ticket Title',
            $key = 'CST-KEY',
            new TicketStatus('IN QA', $statusDateChange->modify('-1 days')),
            new Assignee(
                $name = 'assignee.name',
                $key = 'assignee-key',
                $displayName = 'Full Name'
            ),
            $storyPoints = 5
        );

        $slackMessage = SlackMessage::withTimeToDiff($statusDateChange);
        $this->assertEquals($expectedMessage, $slackMessage->forJiraTicket($jiraTicket, 'company-name'));
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
        $statusDateChange = new DateTimeImmutable();

        $jiraTicket = new JiraTicket(
            $title = 'Ticket Title',
            $key = 'CST-KEY',
            new TicketStatus('IN QA', $statusDateChange->modify('-2 days')),
            Assignee::empty(),
            $storyPoints = 5
        );

        $slackMessage = SlackMessage::withTimeToDiff($statusDateChange);
        $this->assertEquals($expectedMessage, $slackMessage->forJiraTicket($jiraTicket, 'company-name'));
    }
}
