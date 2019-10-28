<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierCommand;
use App\ScrumMaster\Command\SlackNotifierInput;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\Tests\Unit\ScrumMaster\Concerns\JiraApiResource;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SlackNotifierCommandTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $output = new InMemoryOutput();

        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->createMock(HttpClientInterface::class)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $command->execute(SlackNotifierInput::fromArray([
            SlackNotifierInput::COMPANY_NAME => 'company',
            SlackNotifierInput::JIRA_PROJECT_NAME => 'project',
            SlackNotifierInput::DAYS_FOR_STATUS => '{"status":1}',
            SlackNotifierInput::SLACK_MAPPING_IDS => '{"jira.id":"slack.id"}',
        ]), $output);

        $this->assertContains('Total notifications: 0', $output->lines());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $output = new InMemoryOutput();

        $jiraIssues = [
            $this->createAnIssueAsArray('user.1.jira', 'KEY-1'),
            $this->createAnIssueAsArray('user.2.jira', 'KEY-2'),
        ];

        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $command->execute(SlackNotifierInput::fromArray([
            SlackNotifierInput::COMPANY_NAME => 'company',
            SlackNotifierInput::JIRA_PROJECT_NAME => 'project',
            SlackNotifierInput::DAYS_FOR_STATUS => '{"status":1}',
            SlackNotifierInput::SLACK_MAPPING_IDS => '{"jira.id":"slack.id"}',
        ]), $output);

        $this->assertContains('Total notifications: 2', $output->lines());
    }
}
