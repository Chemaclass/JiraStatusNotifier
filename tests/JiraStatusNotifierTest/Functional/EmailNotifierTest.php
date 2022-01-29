<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Functional;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Email;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Email\AddressGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Email\EmailChannel;
use Chemaclass\JiraStatusNotifier\Domain\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Domain\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\Domain\JiraConnector;
use Chemaclass\JiraStatusNotifierTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Twig\Environment;

final class EmailNotifierTest extends TestCase
{
    use JiraApiResource;

    /**
     * @test
     */
    public function zero_notifications_were_sent(): void
    {
        $jiraConnector = $this->createJiraConnector([], []);
        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\EmailChannel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /**
     * @test
     */
    public function two_successful_notifications_were_sent(): void
    {
        $jiraConnector = $this->createJiraConnector(
            jiraIssues: [
                $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
                $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
            ],
            jiraIdsToEmail: [
                'user.1.jira' => '1@email.com',
                'user.2.jira' => '2@email.com',
            ]
        );

        $result = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\EmailChannel::class];
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
            jiraIdsToEmail: [
                'user.1.jira' => '1@email.com',
                'user.2.jira' => '2@email.com',
            ],
            jiraUsersToIgnore: ['user.1.jira']
        );

        $result = $jiraConnector->handle();

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\EmailChannel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /**
     * @test
     */
    public function ensure_proper_response_status_code_per_issue(): void
    {
        $code = 12345;

        /** @var MockObject|TransportInterface $transport */
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects(self::once())
            ->method('send')
            ->willThrowException(new TransportException('', $code));

        $jiraConnector = new JiraConnector(
            new JiraHttpClient(
                $this->mockJiraClient([
                    $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
                ]),
                new JiraTicketsFactory()
            ),
            $this->createJiraConnectorInput(),
            [
                new EmailChannel(
                    new Mailer($transport),
                    $this->createMessageGenerator(),
                    new AddressGenerator([
                        'user.1.jira' => '1@email.com',
                    ])
                ),
            ],
        );

        $results = $jiraConnector->handle();
        /** @var ChannelResult $channelResult */
        $channelResult = $results[Email\EmailChannel::class];
        /** @var ChannelIssue $issue */
        $issue = $channelResult->channelIssues()['KEY-111'];
        self::assertEquals($code, $issue->responseStatusCode());
    }

    /**
     * @test
     */
    public function same_user_receive_one_single_notification(): void
    {
        /** @var MockObject|TransportInterface $transport */
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects(self::exactly(2))->method('send');

        $jiraConnector = new JiraConnector(
            new JiraHttpClient(
                $this->mockJiraClient([
                    $this->createAJiraIssueAsArray('user.1.jira', 'KEY-1', 'status1'),
                    $this->createAJiraIssueAsArray('user.1.jira', 'KEY-2', 'status1'),
                    $this->createAJiraIssueAsArray('user.1.jira', 'KEY-3', 'status2'),
                    $this->createAJiraIssueAsArray('user.2.jira', 'KEY-4', 'status1'),
                ]),
                new JiraTicketsFactory()
            ),
            $this->createJiraConnectorInput(),
            [
                new Email\EmailChannel(
                    new Mailer($transport),
                    $this->createMessageGenerator(),
                    new Email\AddressGenerator([
                        'user.1.jira' => 'email1@a.com',
                        'user.2.jira' => 'email2@a.com',
                    ])
                ),
            ]
        );

        $jiraConnector->handle();
    }

    private function createJiraConnector(
        array $jiraIssues,
        array $jiraIdsToEmail,
        array $jiraUsersToIgnore = []
    ): JiraConnector {
        return new JiraConnector(
            new JiraHttpClient($this->mockJiraClient($jiraIssues), new JiraTicketsFactory()),
            $this->createJiraConnectorInput($jiraUsersToIgnore),
            [
                new Email\EmailChannel(
                    new Mailer($this->createMock(TransportInterface::class)),
                    $this->createMessageGenerator(),
                    new AddressGenerator($jiraIdsToEmail)
                ),
            ]
        );
    }

    private function createJiraConnectorInput(array $jiraUsersToIgnore = []): JiraConnectorInput
    {
        return (new JiraConnectorInput())
            ->setCompanyName('company.name')
            ->setJiraProjectName('Jira project name')
            ->setDaysForStatus(['status1' => 1, 'status2' => 2])
            ->setJiraUsersToIgnore($jiraUsersToIgnore);
    }

    private function createMessageGenerator(): MessageGenerator
    {
        return new MessageGenerator(
            new DateTimeImmutable(),
            $this->createMock(Environment::class),
            'templateName.twig'
        );
    }
}
