<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Functional;

use Chemaclass\ScrumMaster\Channel\Slack\MessageGenerator;
use Chemaclass\ScrumMaster\Channel\Slack\SlackChannel;
use Chemaclass\ScrumMaster\Channel\Slack\SlackChannelResult;
use Chemaclass\ScrumMaster\Channel\Slack\SlackHttpClient;
use Chemaclass\ScrumMaster\Channel\Slack\SlackMapping;
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
        /** @var SlackChannelResult $channelResult */
        $channelResult = $result[SlackChannel::class];
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
        /** @var SlackChannelResult $channelResult */
        $channelResult = $result[SlackChannel::class];
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
            $this->notifierInput([
                NotifierInput::JIRA_USERS_TO_IGNORE => '["user.1.jira"]',
            ])
        );

        /** @var SlackChannelResult $channelResult */
        $channelResult = $result[SlackChannel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    private function notifierInput(array $optionalFields = []): NotifierInput
    {
        return NotifierInput::fromArray(array_merge(self::MANDATORY_FIELDS, $optionalFields));
    }

    private function slackNotifierCommandWithJiraTickets(array $jiraIssues): NotifierCommand
    {
        return new NotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            [
                new SlackChannel(
                    new SlackHttpClient($this->createMock(HttpClientInterface::class)),
                    SlackMapping::jiraNameWithSlackId(['jira.id' => 'slack.id']),
                    MessageGenerator::withTimeToDiff(new DateTimeImmutable())
                ),
            ]
        );
    }
}
