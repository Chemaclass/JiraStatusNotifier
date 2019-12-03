<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Functional;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\Slack;
use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SlackNotifierCommandTest extends TestCase
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
        $command = $this->slackNotifierCommandWithJiraTickets([]);
        $result = $command->execute($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Slack\Channel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $command = $this->slackNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $command->execute($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Slack\Channel::class];
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /** @test */
    public function ignoredUserShouldNotReceiveAnyNotification(): void
    {
        $command = $this->slackNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $command->execute(
            $this->notifierInput($usersToIgnore = ['user.1.jira'])
        );

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Slack\Channel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    private function notifierInput(array $jiraUsersToIgnore = []): NotifierInput
    {
        return NotifierInput::new('company.name', 'Jira project name', ['status' => 1], $jiraUsersToIgnore);
    }

    private function slackNotifierCommandWithJiraTickets(array $jiraIssues): NotifierCommand
    {
        return new NotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            [
                new Slack\Channel(
                    new Slack\HttpClient($this->createMock(HttpClientInterface::class)),
                    Slack\JiraMapping::jiraNameWithSlackId(['jira.id' => 'slack.id']),
                    Slack\MessageGenerator::withTimeToDiff(new DateTimeImmutable())
                ),
            ]
        );
    }
}
