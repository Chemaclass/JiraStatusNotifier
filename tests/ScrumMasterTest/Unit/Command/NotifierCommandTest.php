<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResultInterface;
use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class NotifierCommandTest extends TestCase
{
    use JiraApiResource;

    private const MANDATORY_FIELDS = [
        NotifierInput::COMPANY_NAME => 'company.name',
        NotifierInput::JIRA_PROJECT_NAME => 'Jira project name',
        NotifierInput::DAYS_FOR_STATUS => '{"status":1}',
    ];

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $command = $this->notifierCommandWithJiraTickets([]);
        $result = $command->execute($this->notifierInput());
        $this->assertEmpty(reset($result)->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $command = $this->notifierCommandWithJiraTickets([
            $this->createAnIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAnIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $command->execute($this->notifierInput());
        $this->assertEquals(['KEY-111', 'KEY-222'], reset($result)->channelIssuesKeys());
    }

    private function notifierInput(): NotifierInput
    {
        return NotifierInput::fromArray(self::MANDATORY_FIELDS);
    }

    private function notifierCommandWithJiraTickets(array $jiraIssues): NotifierCommand
    {
        /** @var ChannelResultInterface|MockObject $channel */
        $result = $this->createMock(ChannelResultInterface::class);
        $result->method('channelIssuesKeys')->willReturn(array_map(function (array $jiraIssue) {
            return $jiraIssue['key'];
        }, $jiraIssues));

        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('sendNotifications')->willReturn($result);

        return new NotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            [$channel]
        );
    }
}
