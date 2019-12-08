<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Notifier;
use Chemaclass\ScrumMaster\IO\NotifierInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class NotifierTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $notifier = $this->notifierCommandWithChannelIssues([]);
        $result = $notifier->notify($this->notifierInput());
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

        $notifier = $this->notifierCommandWithChannelIssues($issues);
        $result = $notifier->notify($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = reset($result);
        $this->assertEquals($issues, $channelResult->channelIssues());
    }

    private function notifierInput(): NotifierInput
    {
        return NotifierInput::new('company.name', 'Jira project name', ['status' => 1]);
    }

    private function notifierCommandWithChannelIssues(array $channelIssues): Notifier
    {
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('sendNotifications')->willReturn(ChannelResult::withIssues($channelIssues));

        return new Notifier(
            new JiraHttpClient($this->mockJiraClient([])),
            [$channel]
        );
    }
}
