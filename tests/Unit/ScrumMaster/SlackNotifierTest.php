<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Jira\UrlFactoryInterface;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use App\ScrumMaster\SlackNotifier;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierTest extends TestCase
{
    /** @test */
    public function noNotificationsAreSentOutIfNoJiraIssuesWhereFound(): void
    {
        $jiraBoard = new Board(['status1' => 1]);

        $jiraResponse = $this->createMock(ResponseInterface::class);
        $jiraResponse->method('toArray')->willReturn([
            'issues' => [],
        ]);

        $jiraClient = $this->createMock(HttpClientInterface::class);
        $jiraClient->method('request')->willReturn($jiraResponse);

        $slackNotifier = new SlackNotifier(
            $jiraBoard,
            new JiraHttpClient(
                $jiraClient,
                $this->createMock(UrlFactoryInterface::class)
            ),
            new SlackHttpClient(
                $this->createMock(HttpClientInterface::class)
            )
        );

        $responses = $slackNotifier->sendNotifications(
            new CompanyProject(
                'COMPANY_NAME',
                'JIRA_PROJECT_NAME'
            ),
            new SlackMapping(['key' => 'value']),
            new SlackMessage(new DateTimeImmutable())
        );

        $this->assertEmpty($responses, 'No notifications should have been sent');
    }
}
