<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Functional;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Cli;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Cli\CliChannel;
use Chemaclass\JiraStatusNotifier\Domain\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\Domain\JiraConnector;
use Chemaclass\JiraStatusNotifierTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\TestCase;

final class CliNotifierTest extends TestCase
{
    use JiraApiResource;

    /**
     * @test
     */
    public function zero_notifications_were_sent(): void
    {
        $jiraConnector = $this->createJiraConnector([]);
        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\CliChannel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /**
     * @test
     */
    public function two_successful_notifications_were_sent(): void
    {
        $jiraConnector = $this->createJiraConnector([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\CliChannel::class];
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /**
     * @test
     */
    public function ignored_user_should_not_receive_any_notification(): void
    {
        $jiraConnector = $this->createJiraConnector([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ], jiraUsersToIgnore: ['user.1.jira']);

        $result = $jiraConnector->handle();

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\CliChannel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    private function createJiraConnector(array $jiraIssues, array $jiraUsersToIgnore = []): JiraConnector
    {
        return new JiraConnector(
            new JiraHttpClient($this->mockJiraClient($jiraIssues), new JiraTicketsFactory()),
            $this->createJiraConnectorInput($jiraUsersToIgnore),
            [
                new CliChannel(),
            ]
        );
    }

    private function createJiraConnectorInput(array $jiraUsersToIgnore = []): JiraConnectorInput
    {
        return (new JiraConnectorInput())
            ->setCompanyName('company.name')
            ->setJiraProjectName('Jira project name')
            ->setDaysForStatus(['status' => 1])
            ->setJiraUsersToIgnore($jiraUsersToIgnore);
    }
}
