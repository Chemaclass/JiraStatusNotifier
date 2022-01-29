<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Functional;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Slack;
use Chemaclass\JiraStatusNotifier\Domain\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\Domain\JiraConnector;
use Chemaclass\JiraStatusNotifierTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

final class SlackNotifierTest extends TestCase
{
    use JiraApiResource;

    /**
     * @test
     */
    public function zero_notifications_were_sent(): void
    {
        $jiraConnector = $this->createJiraConnector([]);
        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Slack\SlackChannel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /**
     * @test
     */
    public function two_successful_notifications_were_sent(): void
    {
        $jiraConnector = $this->createJiraConnector([
            $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
            $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
        ]);

        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Slack\SlackChannel::class];
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /**
     * @test
     */
    public function ignored_user_should_not_receive_any_notification(): void
    {
        $jiraConnector = $this->createJiraConnector(
            jiraIssues: [
                $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
                $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
            ],
            jiraUsersToIgnore: ['user.1.jira']
        );

        $result = $jiraConnector->handle();

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Slack\SlackChannel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    private function createJiraConnector(array $jiraIssues, array $jiraUsersToIgnore = []): JiraConnector
    {
        return new JiraConnector(
            new JiraHttpClient(
                $this->mockJiraClient($jiraIssues),
                new JiraTicketsFactory()
            ),
            $this->createJiraConnectorInput($jiraUsersToIgnore),
            [
                new Slack\SlackChannel(
                    new Slack\HttpClient($this->createMock(HttpClientInterface::class)),
                    Slack\JiraMapping::jiraNameWithSlackId(['jira.id' => 'slack.id']),
                    new MessageGenerator(
                        new DateTimeImmutable(),
                        $this->createMock(Environment::class),
                        'template-name.twig'
                    )
                ),
            ]
        );
    }

    private function createJiraConnectorInput(array $jiraUsersToIgnore = []): JiraConnectorInput
    {
        return (new JiraConnectorInput())
            ->setCompanyName('company.name')
            ->setJiraProjectName('Jira project name')
            ->setDaysForStatus(['status1' => 1])
            ->setJiraUsersToIgnore($jiraUsersToIgnore);
    }
}
