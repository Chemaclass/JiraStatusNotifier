<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Functional;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\Cli;
use Chemaclass\ScrumMaster\IO\JiraConnectorInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JiraTicketsFactory;
use Chemaclass\ScrumMaster\JiraConnector;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\TestCase;

final class CliNotifierTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $jiraConnector = $this->cliNotifierCommandWithJiraTickets([]);
        $result = $jiraConnector->handle($this->jiraConnectorInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\Channel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $jiraConnector = $this->cliNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $jiraConnector->handle($this->jiraConnectorInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\Channel::class];
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /** @test */
    public function ignoredUserShouldNotReceiveAnyNotification(): void
    {
        $jiraConnector = $this->cliNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $jiraConnector->handle($this->jiraConnectorInput($usersToIgnore = ['user.1.jira']));

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\Channel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    private function jiraConnectorInput(array $jiraUsersToIgnore = []): JiraConnectorInput
    {
        return JiraConnectorInput::new(
            'company.name',
            'Jira project name',
            ['status' => 1],
            $jiraUsersToIgnore
        );
    }

    private function cliNotifierCommandWithJiraTickets(array $jiraIssues): JiraConnector
    {
        return new JiraConnector(
            new JiraHttpClient($this->mockJiraClient($jiraIssues), new JiraTicketsFactory()),
            [new Cli\Channel()]
        );
    }
}
