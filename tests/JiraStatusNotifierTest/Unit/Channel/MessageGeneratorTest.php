<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Channel;

use Chemaclass\JiraStatusNotifier\Domain\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifierTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Twig;

final class MessageGeneratorTest extends TestCase
{
    use JiraApiResource;

    /**
     * @test
     */
    public function for_jira_tickets(): void
    {
        $tickets = (new JiraTicketsFactory())->fromArrayIssues([
            $this->createAJiraIssueAsArray('$assigneeKey', '$email'),
        ]);
        $now = new DateTimeImmutable();
        $companyName = 'Any company name';
        $templateName = 'template-name.twig';

        $twigMock = $this->createMock(Twig\Environment::class);
        $twigMock->expects(self::once())->method('render')->with(
            $this->equalTo($templateName),
            $this->equalTo([
                'tickets' => $tickets,
                'now' => $now,
                'companyName' => $companyName,
            ])
        );

        $generator = new MessageGenerator($now, $twigMock, $templateName);
        $generator->forJiraTickets($companyName, ...$tickets);
    }
}
