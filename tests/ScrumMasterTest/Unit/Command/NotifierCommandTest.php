<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
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
        $command = $this->notifierCommandWithChannelIssues([]);
        $result = $command->execute($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = reset($result);
        $this->assertEmpty($channelResult->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $issues = [
            'KEY-1' => ChannelIssue::withCodeAndAssignee(200, 'jira.user.1'),
            'KEY-2' => ChannelIssue::withCodeAndAssignee(200, 'jira.user.2'),
        ];

        $command = $this->notifierCommandWithChannelIssues($issues);
        $result = $command->execute($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = reset($result);
        $this->assertEquals($issues, $channelResult->channelIssues());
    }

    private function notifierInput(): NotifierInput
    {
        return NotifierInput::fromArray(self::MANDATORY_FIELDS);
    }

    private function notifierCommandWithChannelIssues(array $channelIssues): NotifierCommand
    {
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('sendNotifications')->willReturn(ChannelResult::withIssues($channelIssues));

        return new NotifierCommand(
            new JiraHttpClient($this->mockJiraClient([])),
            [$channel]
        );
    }
}
