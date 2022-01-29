<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Domain\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\Domain\JiraConnector;
use Chemaclass\JiraStatusNotifierTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\TestCase;

final class JiraConnectorTest extends TestCase
{
    use JiraApiResource;

    /**
     * @test
     */
    public function zero_notifications_were_sent(): void
    {
        $jiraConnector = $this->jiraConnectorCommandWithChannelIssues([]);
        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = reset($result);
        $this->assertEmpty($channelResult->channelIssues());
    }

    /**
     * @test
     */
    public function two_successful_notifications_were_sent(): void
    {
        $issues = [
            'KEY-1' => ChannelIssue::withCodeAndAssignee(200, 'jira.user.1'),
            'KEY-2' => ChannelIssue::withCodeAndAssignee(200, 'jira.user.2'),
        ];

        $jiraConnector = $this->jiraConnectorCommandWithChannelIssues($issues);
        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = reset($result);
        $this->assertEquals($issues, $channelResult->channelIssues());
    }

    private function jiraConnectorCommandWithChannelIssues(array $channelIssues): JiraConnector
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('send')->willReturn(ChannelResult::withIssues($channelIssues));

        return new JiraConnector(
            new JiraHttpClient($this->mockJiraClient([]), new JiraTicketsFactory()),
            $this->jiraConnectorInput(),
            [$channel]
        );
    }

    private function jiraConnectorInput(): JiraConnectorInput
    {
        return (new JiraConnectorInput())
            ->setCompanyName('company.name')
            ->setJiraProjectName('Jira project name')
            ->setDaysForStatus(['status' => 1]);
    }
}
