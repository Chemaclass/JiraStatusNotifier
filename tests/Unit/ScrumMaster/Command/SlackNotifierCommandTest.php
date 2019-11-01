<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierCommand;
use App\ScrumMaster\Command\SlackNotifierInput;
use App\ScrumMaster\Command\SlackNotifierOutput;
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
        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->createMock(HttpClientInterface::class)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $result = $command->execute(SlackNotifierInput::fromArray([
            SlackNotifierInput::COMPANY_NAME => 'company',
            SlackNotifierInput::JIRA_PROJECT_NAME => 'project',
            SlackNotifierInput::DAYS_FOR_STATUS => '{"status":1}',
            SlackNotifierInput::SLACK_MAPPING_IDS => '{"jira.id":"slack.id"}',
        ]), $this->inMemoryOutput());

        $this->assertEmpty($result->list());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $jiraIssues = [
            $this->createAnIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAnIssueAsArray('user.2.jira', 'KEY-222'),
        ];

        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $inMemoryOutput = new InMemoryOutput();

        $result = $command->execute(SlackNotifierInput::fromArray([
            SlackNotifierInput::COMPANY_NAME => 'company',
            SlackNotifierInput::JIRA_PROJECT_NAME => 'project',
            SlackNotifierInput::DAYS_FOR_STATUS => '{"status":1}',
            SlackNotifierInput::SLACK_MAPPING_IDS => '{"jira.id":"slack.id"}',
        ]), new SlackNotifierOutput($inMemoryOutput));

        $this->assertNotEmpty($inMemoryOutput->lines());
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($result->list()));
    }

    private function inMemoryOutput(): SlackNotifierOutput
    {
        return new SlackNotifierOutput(new InMemoryOutput());
    }
}
