<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Slack\MessageTemplate\SlackMessage;
use Chemaclass\ScrumMaster\Slack\SlackChannel;
use Chemaclass\ScrumMaster\Slack\SlackHttpClient;
use Chemaclass\ScrumMaster\Slack\SlackMapping;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
        $command = $this->slackNotifierCommandWithJiraTickets([]);
        $result = $command->execute($this->notifierInput());
        $this->assertEmpty($result[SlackChannel::class]->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $command = $this->slackNotifierCommandWithJiraTickets([
            $this->createAnIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAnIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $command->execute($this->notifierInput());
        $this->assertEquals(['KEY-111', 'KEY-222'], $result[SlackChannel::class]->ticketKeys());
    }

    /** @test */
    public function ignoredUserShouldNotReceiveAnyNotification(): void
    {
        $command = $this->slackNotifierCommandWithJiraTickets([
            $this->createAnIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAnIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $command->execute(
            $this->notifierInput([
                NotifierInput::JIRA_USERS_TO_IGNORE => '["user.1.jira"]',
            ])
        );

        $this->assertEquals(['KEY-222'], $result[SlackChannel::class]->ticketKeys());
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
                    SlackMessage::withTimeToDiff(new DateTimeImmutable())
                ),
            ]
        );
    }
}
