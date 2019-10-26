<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster;

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Jira\UrlFactoryInterface;
use App\ScrumMaster\Slack\MessageGeneratorInterface;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\SlackNotifier;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierTest extends TestCase
{
    /** @test */
    public function noNotificationsAreSentOutIfNoJiraIssuesWhereFound(): void
    {
        $jiraBoard = new Board(['status1' => 1]);

        $slackNotifier = new SlackNotifier(
            $jiraBoard,
            new JiraHttpClient(
                $this->mockJiraClient($issues = []),
                $this->createMock(UrlFactoryInterface::class)
            ),
            new SlackHttpClient(
                $this->createMock(HttpClientInterface::class)
            )
        );

        $responses = $slackNotifier->sendNotifications(
            $this->aCompanyProject(),
            SlackMapping::jiraNameWithSlackId(['key' => 'value']),
            $this->createMock(MessageGeneratorInterface::class)
        );

        $this->assertEmpty($responses, 'No notifications should have been sent');
    }

    /** @test */
    public function notificationsAreSentOutIfJiraIssuesWhereFound(): void
    {
        $jiraBoard = new Board(['status1' => 1]);

        $mockSlackClient = $this->createMock(HttpClientInterface::class);
        $mockSlackClient->expects($this->once())->method('request')->with(
            $this->equalTo('POST'),
            $this->equalTo(SlackHttpClient::SLACK_API_POST_MESSAGE),
            $this->equalTo([
                'json' => [
                    'as_user' => true,
                    'channel' => 'channel.id',
                    'text' => 'any text',
                ],
            ])
        );

        $slackNotifier = new SlackNotifier(
            $jiraBoard,
            new JiraHttpClient(
                $this->mockJiraClient($issues = [
                    [
                        'key' => 'KEY-123',
                        'fields' => [
                            'customfield_10005' => '5.0',
                            'status' => [
                                'name' => 'In Progress',
                            ],
                            'summary' => 'The ticket title',
                            'statuscategorychangedate' => '2019-06-15T10:35:00+00',
                            'assignee' => [
                                'name' => 'username.jira',
                                'key' => 'user.key.jira',
                                'emailAddress' => 'user@email.jira',
                                'displayName' => 'display.name.jira',
                            ],
                        ],
                    ],
                ]),
                $this->createMock(UrlFactoryInterface::class)
            ),
            new SlackHttpClient($mockSlackClient)
        );

        $messageGenerator = $this->createMock(MessageGeneratorInterface::class);
        $messageGenerator->method('forJiraTicket')->willReturn('any text');

        $responses = $slackNotifier->sendNotifications(
            $this->aCompanyProject(),
            SlackMapping::jiraNameWithSlackId(['username.jira' => 'channel.id']),
            $messageGenerator
        );

        $this->assertNotEmpty($responses, '1 notification should have been sent');
    }

    private function mockJiraClient(array $issues): HttpClientInterface
    {
        $jiraResponse = $this->createMock(ResponseInterface::class);
        $jiraResponse->method('toArray')->willReturn(['issues' => $issues]);

        $jiraClient = $this->createMock(HttpClientInterface::class);
        $jiraClient->method('request')->willReturn($jiraResponse);

        return $jiraClient;
    }

    private function aCompanyProject(): CompanyProject
    {
        return new CompanyProject('COMPANY_NAME', 'JIRA_PROJECT_NAME');
    }
}
