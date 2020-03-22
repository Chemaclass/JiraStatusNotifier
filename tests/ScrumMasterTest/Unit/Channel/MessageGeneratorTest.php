<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel;

use Chemaclass\ScrumMaster\Channel\MessageGenerator;
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
        $this->expectException(InvalidArgumentException::class);

        $generator = new MessageGenerator(
            new DateTimeImmutable(),
            $this->createMock(Twig\Environment::class),
            'template-name.twig'
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
        $generator->forJiraTickets($tickets, $companyName);
    }
}
