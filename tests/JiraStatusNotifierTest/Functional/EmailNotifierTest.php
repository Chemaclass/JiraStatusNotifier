<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Functional;

use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\Email;
use Chemaclass\JiraStatusNotifier\Channel\Email\AddressGenerator;
use Chemaclass\JiraStatusNotifier\Channel\Email\Channel;
use Chemaclass\JiraStatusNotifier\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\JiraConnector;
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

    /** @test */
    public function zeroNotificationsWereSent(): void
    {
        $jiraConnector = $this->emailNotifierCommandWithJiraTickets([], []);
        $result = $jiraConnector->handle($this->jiraConnectorInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\Channel::class];
        $this->assertEmpty($channelResult->channelIssues());
    }

    /** @test */
    public function twoSuccessfulNotificationsWereSent(): void
    {
        $jiraConnector = $this->emailNotifierCommandWithJiraTickets(
            [
                $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
                $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
            ],
            [
                'user.1.jira' => '1@email.com',
                'user.2.jira' => '2@email.com',
            ]
        );

        $result = $jiraConnector->handle($this->jiraConnectorInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\Channel::class];
        $this->assertEquals(['KEY-111', 'KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /** @test */
    public function ignoredUserShouldNotReceiveAnyNotification(): void
    {
        $jiraConnector = $this->emailNotifierCommandWithJiraTickets(
            [
                $this->createAJiraIssueAsArray('user.1.jira', 'KEY-111'),
                $this->createAJiraIssueAsArray('user.2.jira', 'KEY-222'),
            ],
            [
                'user.1.jira' => '1@email.com',
                'user.2.jira' => '2@email.com',
            ]
        );

        $result = $jiraConnector->handle(
            $this->jiraConnectorInput($usersToIgnore = ['user.1.jira'])
        );

        /** @var ChannelResult $channelResult */
        $channelResult = $result[Email\Channel::class];
        $this->assertEquals(['KEY-222'], array_keys($channelResult->channelIssues()));
    }

    /** @test */
    public function ensureProperResponseStatusCodePerIssue(): void
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
            new Channel(new Mailer($transport), $this->messageGenerator(), new AddressGenerator([
                'user.1.jira' => '1@email.com',
            ])),
        );

        $results = $jiraConnector->handle($this->jiraConnectorInput());
        /** @var ChannelResult $channelResult */
        $channelResult = $results[Email\Channel::class];
        /** @var ChannelIssue $issue */
        $issue = $channelResult->channelIssues()['KEY-111'];
        self::assertEquals($code, $issue->responseStatusCode());
    }

    /** @test */
    public function sameUserReceiveOneSingleNotification(): void
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
            new Email\Channel(
                new Mailer($transport),
                $this->messageGenerator(),
                new Email\AddressGenerator([
                    'user.1.jira' => 'email1@a.com',
                    'user.2.jira' => 'email2@a.com',
                ])
            )
        );

        $jiraConnector->handle($this->jiraConnectorInput());
    }

    private function jiraConnectorInput(array $jiraUsersToIgnore = []): JiraConnectorInput
    {
        return JiraConnectorInput::new(
            'company.name',
            'Jira project name',
            ['status1' => 1, 'status2' => 2],
            $jiraUsersToIgnore
        );
    }

    private function emailNotifierCommandWithJiraTickets(array $jiraIssues, array $jiraIdsToEmail): JiraConnector
    {
        return new JiraConnector(
            new JiraHttpClient(
                $this->mockJiraClient($jiraIssues),
                new JiraTicketsFactory()
            ),
            new Email\Channel(
                new Mailer($this->createMock(TransportInterface::class)),
                $this->messageGenerator(),
                new AddressGenerator($jiraIdsToEmail)
            )
        );
    }

    private function messageGenerator(): MessageGenerator
    {
        return new MessageGenerator(
            new DateTimeImmutable(),
            $this->createMock(Environment::class),
            'templateName.twig'
        );
    }
}
