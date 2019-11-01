<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierOutput;
use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use App\ScrumMaster\Jira\ReadModel\TicketStatus;
use App\ScrumMaster\Slack\SlackNotifierResult;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierOutputTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = new SlackNotifierResult();
        $result->addTicketWithResponse($this->newTicket('KEY-2a'), $this->responseWithStatusCode(200));
        $result->addTicketWithResponse($this->newTicket('KEY-2b'), $this->responseWithStatusCode(200));
        $result->addTicketWithResponse($this->newTicket('KEY-3'), $this->responseWithStatusCode(300));
        $result->addTicketWithResponse($this->newTicket('KEY-4'), $this->responseWithStatusCode(400));

        $output = new InMemoryOutput();
        (new SlackNotifierOutput($output))->write($result);

        $this->assertContains('Total notifications: 4 (KEY-2a, KEY-2b, KEY-3, KEY-4)', $output->lines());
        $this->assertContains('Total successful notifications sent: 2', $output->lines());
        // FIXME: currently is `1` because it counts as failed only the ones with statusCode:400
        $this->assertContains('Total failed notifications sent: 1', $output->lines());
    }

    private function newTicket(string $key): JiraTicket
    {
        return new JiraTicket(
            'Ticket Title',
            $key,
            new TicketStatus('IN QA', new DateTimeImmutable()),
            new Assignee(
                'assignee.name',
                'assignee-key',
                'Display Name'
            ),
            $storyPoints = 5
        );
    }

    private function responseWithStatusCode(int $statusCode): ResponseInterface
    {
        /** @var MockObject|ResponseInterface $response */
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);

        return $response;
    }
}
