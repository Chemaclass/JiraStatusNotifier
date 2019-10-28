<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierCommand;
use App\ScrumMaster\Command\SlackNotifierInput;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\Tickets;
use App\ScrumMaster\Slack\SlackHttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierCommandTest extends TestCase
{
    /** @test */
    public function zeroSuccessfulNotificationsWereSent(): void
    {
        $output = new InMemoryOutput();

        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->createMock(HttpClientInterface::class)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $command->execute(SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'JIRA_PROJECT_NAME' => 'project',
            'DAYS_FOR_STATUS' => '{"status":1}',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]), $output);

        $this->assertEquals([
            'Total notifications: 0',
            'Total successful notifications sent: 0',
            'Total failed notifications sent: 0',
        ], $output->lines());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $output = new InMemoryOutput();
        $jiraIssues = [
            $this->createAnIssueAsArray('user.1.jira'),
            $this->createAnIssueAsArray('user.2.jira'),
        ];
        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $command->execute(SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'JIRA_PROJECT_NAME' => 'project',
            'DAYS_FOR_STATUS' => '{"status":1}',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]), $output);

        $this->assertEquals('Total notifications: 2', $output->lines()[0]);
    }

    private function mockJiraClient(array $issues): HttpClientInterface
    {
        $jiraResponse = $this->createMock(ResponseInterface::class);
        $jiraResponse->method('toArray')->willReturn(['issues' => $issues]);

        /** @var HttpClientInterface|MockObject $jiraClient */
        $jiraClient = $this->createMock(HttpClientInterface::class);
        $jiraClient->method('request')->willReturn($jiraResponse);

        return $jiraClient;
    }

    private function createAnIssueAsArray(string $assigneeName): array
    {
        return [
            'key' => 'KEY-123',
            'fields' => [
                Tickets::FIELD_STORY_POINTS => '5.0',
                'status' => [
                    'name' => 'In Progress',
                ],
                'summary' => 'The ticket title',
                'statuscategorychangedate' => '2019-06-15T10:35:00+00',
                'assignee' => [
                    'name' => $assigneeName,
                    'key' => 'user.key.jira',
                    'emailAddress' => 'user@email.jira',
                    'displayName' => 'display.name.jira',
                ],
            ],
        ];
    }
}
