<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Functional;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\Cli;
use Chemaclass\ScrumMaster\IO\NotifierInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Notifier;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use PHPUnit\Framework\TestCase;

final class CliNotifierTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $notifier = $this->cliNotifierCommandWithJiraTickets([]);
        $result = $notifier->notify($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\Channel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $notifier = $this->cliNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $notifier->notify($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\Channel::class];
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /** @test */
    public function ignoredUserShouldNotReceiveAnyNotification(): void
    {
        $notifier = $this->cliNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $notifier->notify($this->notifierInput($usersToIgnore = ['user.1.jira']));

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Cli\Channel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    private function notifierInput(array $jiraUsersToIgnore = []): NotifierInput
    {
        return NotifierInput::new(
            'company.name',
            'Jira project name',
            ['status' => 1],
            $jiraUsersToIgnore
        );
    }

    private function cliNotifierCommandWithJiraTickets(array $jiraIssues): Notifier
    {
        return new Notifier(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            [new Cli\Channel()]
        );
    }
}
