<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Command;

use Chemaclass\JiraStatusNotifier\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\JiraConnector;
use Chemaclass\JiraStatusNotifierTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class JiraConnectorTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $jiraConnector = $this->jiraConnectorCommandWithChannelIssues([]);
        $result = $jiraConnector->handle($this->jiraConnectorInput());
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

        $jiraConnector = $this->jiraConnectorCommandWithChannelIssues($issues);
        $result = $jiraConnector->handle($this->jiraConnectorInput());
        /** @var ChannelResult $channelResult */
        $channelResult = reset($result);
        $this->assertEquals($issues, $channelResult->channelIssues());
    }

    private function jiraConnectorInput(): JiraConnectorInput
    {
        return JiraConnectorInput::new('company.name', 'Jira project name', ['status' => 1]);
    }

    private function jiraConnectorCommandWithChannelIssues(array $channelIssues): JiraConnector
    {
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('send')->willReturn(ChannelResult::withIssues($channelIssues));

        return new JiraConnector(
            new JiraHttpClient($this->mockJiraClient([]), new JiraTicketsFactory()),
            [$channel]
        );
    }
}
