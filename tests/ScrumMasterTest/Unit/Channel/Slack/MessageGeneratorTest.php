<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\Slack\MessageGenerator;
use Chemaclass\ScrumMaster\Jira\Tickets;
use Chemaclass\ScrumMasterTests\Unit\Concerns\JiraApiResource;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Twig;

final class MessageGeneratorTest extends TestCase
{
    use JiraApiResource;

    /** @test */
    public function forJiraTicketsWrongType(): void
    {
        self::expectException(InvalidArgumentException::class);

        $generator = new MessageGenerator(
            new DateTimeImmutable(),
            self::createMock(Twig\Environment::class)
        );

        $invalidTypes = [new \stdClass()];
        $generator->forJiraTickets($invalidTypes, 'Any company name');
    }

    /** @test */
    public function forJiraTickets(): void
    {
        $tickets = Tickets::fromArrayIssues([
            $this->createAJiraIssueAsArray('$assigneeKey', '$email'),
        ]);
        $companyName = 'Any company name';
        $now = new DateTimeImmutable();

        $twigMock = self::createMock(Twig\Environment::class);
        $twigMock->expects(self::once())->method('render')->with(
            $this->equalTo(MessageGenerator::TEMPLATE_NAME),
            $this->equalTo([
                'tickets' => $tickets,
                'now' => $now,
                'companyName' => $companyName,
            ])
        );

        $generator = new MessageGenerator($now, $twigMock);
        $generator->forJiraTickets($tickets, $companyName);
    }
}
