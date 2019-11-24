<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Functional;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\Email;
use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;

final class EmailNotifierCommandTest extends TestCase
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
        $channelResult = $result[Email\Channel::class];
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
        $channelResult = $result[Email\Channel::class];
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

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\Channel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /** @test */
    public function overrideEmailFromAssignee(): void
    {
        $command = $this->slackNotifierCommandWithJiraTickets([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111', 'user.1@email.com'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222', 'user.2@email.com'),
        ], Email\ByPassEmail::overriddenEmails(['user.1.jira' => 'user.2@email.com']));

        $result = $command->execute($this->notifierInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\Channel::class];
        $issues = $channelResult->channelIssues();
        $workInProgress = 1;
    }

    private function notifierInput(array $optionalFields = []): NotifierInput
    {
        return NotifierInput::fromArray(array_merge(self::MANDATORY_FIELDS, $optionalFields));
    }

    private function slackNotifierCommandWithJiraTickets(
        array $jiraIssues,
        ?Email\ByPassEmail $byPassEmail = null
    ): NotifierCommand {
        return new NotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            [
                new Email\Channel(
                    new Email\MailerClient($this->createMock(Swift_Mailer::class)),
                    Email\MessageGenerator::withTimeToDiff(new DateTimeImmutable()),
                    $byPassEmail
                ),
            ]
        );
    }
}
