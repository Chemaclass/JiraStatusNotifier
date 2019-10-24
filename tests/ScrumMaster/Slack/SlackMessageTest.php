<?php

declare(strict_types=1);

namespace App\Tests\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use App\ScrumMaster\Slack\SlackMessage;
use PHPUnit\Framework\TestCase;

final class SlackMessageTest extends TestCase
{
    /** @test */
    public function generateAMessageFromATicket(): void
    {
        $expectedMessage = <<<TXT
The ticket "Ticket Title" (CST-KEY)[5SP] is still IN QA since one day.
Assignee to Name Surname (assignee-name), please take of it!


TXT;
        $jiraTickets = [
            new JiraTicket(
                $title = 'Ticket Title',
                $key = 'CST-KEY',
                $status = 'IN QA',
                new Assignee(
                    $name = 'assignee-name',
                    $key = 'assignee-key',
                    $emailAddress = 'person@companymail.com',
                    $displayName = 'Name Surname'
                ),
                $storyPoints = 5
            ),
        ];

        $this->assertEquals($expectedMessage, SlackMessage::fromJiraTickets($jiraTickets));
    }

    /** @test */
    public function generateAMessageFromTwoTicket(): void
    {
        $expectedMessage = <<<TXT
The ticket "Ticket Title" (CST-KEY)[1SP] is still IN REVIEW since one day.
Assignee to Name Surname (assignee-name), please take of it!

The ticket "Ticket Title2" (CST-KEY2)[2SP] is still IN QA since one day.
Assignee to Name Surname2 (assignee-name2), please take of it!


TXT;

        $jiraTickets = [
            new JiraTicket(
                $title = 'Ticket Title',
                $key = 'CST-KEY',
                $status = 'IN REVIEW',
                new Assignee(
                    $name = 'assignee-name',
                    $key = 'assignee-key',
                    $emailAddress = 'person@companymail.com',
                    $displayName = 'Name Surname'
                ),
                $storyPoints = 1
            ),
            new JiraTicket(
                $title = 'Ticket Title2',
                $key = 'CST-KEY2',
                $status = 'IN QA',
                new Assignee(
                    $name = 'assignee-name2',
                    $key = 'assignee-key2',
                    $emailAddress = 'person@companymail.com2',
                    $displayName = 'Name Surname2'
                ),
                $storyPoints = 2
            ),
        ];

        $this->assertEquals($expectedMessage, SlackMessage::fromJiraTickets($jiraTickets));
    }
}
