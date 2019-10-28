<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Slack;

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\Company;
use App\ScrumMaster\Jira\UrlFactoryInterface;
use App\ScrumMaster\Slack\MessageGeneratorInterface;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackNotifier;
use App\Tests\Unit\ScrumMaster\JiraApiResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SlackNotifierTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function noNotificationsAreSentOutIfNoJiraIssuesWhereFound(): void
    {
        $jiraBoard = new Board(['status1' => 1]);

        $slackNotifier = new SlackNotifier(
            $jiraBoard,
            new JiraHttpClient($this->mockJiraClient($issues = [])),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $responses = $slackNotifier->sendNotifications(
            $this->aCompany(),
            $this->createMock(UrlFactoryInterface::class),
            SlackMapping::jiraNameWithSlackId(['key' => 'value']),
            $this->createMock(MessageGeneratorInterface::class)
        );

        $this->assertEmpty($responses, 'No notifications should have been sent');
    }

    /** @test */
    public function notificationsAreSentOutIfJiraIssuesWhereFound(): void
    {
        $jiraBoard = new Board(['status1' => 1]);

        $jiraIssues = [
            $this->createAnIssueAsArray('user.1.jira'),
            $this->createAnIssueAsArray('user.2.jira'),
        ];

        $totalIssues = count($jiraIssues);

        /** @var HttpClientInterface|MockObject $mockSlackClient */
        $mockSlackClient = $this->createMock(HttpClientInterface::class);
        $mockSlackClient->expects($this->exactly($totalIssues))
            ->method('request')
            ->with(
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
            new JiraHttpClient($this->mockJiraClient($jiraIssues)),
            new SlackHttpClient($mockSlackClient)
        );

        $messageGenerator = $this->createMock(MessageGeneratorInterface::class);
        $messageGenerator->expects($this->exactly($totalIssues))
            ->method('forJiraTicket')->willReturn('any text');

        $responses = $slackNotifier->sendNotifications(
            $this->aCompany(),
            $this->createMock(UrlFactoryInterface::class),
            SlackMapping::jiraNameWithSlackId([
                'user.1.jira' => 'channel.id',
                'user.2.jira' => 'channel.id',
                'user.3.jira' => 'other.channel.id',
            ]),
            $messageGenerator
        );

        $this->assertCount($totalIssues, $responses, 'Some notifications should have been sent');
    }

    private function aCompany(): Company
    {
        return Company::withNameAndProject('COMPANY_NAME', 'JIRA_PROJECT_NAME');
    }
}
