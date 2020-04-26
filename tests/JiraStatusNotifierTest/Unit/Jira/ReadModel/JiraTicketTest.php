<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira\ReadModel;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class JiraTicketTest extends TestCase
{
    private JiraTicket $ticket;

    protected function setUp(): void
    {
        $this->ticket = new JiraTicket(
            'title',
            'key',
            new TicketStatus(
                'statusName',
                (new DateTimeImmutable())->setTime(0, 0, 0)
            ),
            new Assignee('accountId_!@#$', 'Display Full Name'),
            ['real_key' => 'customKey']
        );
    }

    /** @test */
    public function title(): void
    {
        self::assertSame('title', $this->ticket->title());
    }

    /** @test */
    public function key(): void
    {
        self::assertSame('key', $this->ticket->key());
    }

    /** @test */
    public function status(): void
    {
        self::assertSame('statusName', $this->ticket->status()->name());

        self::assertEquals(
            (new DateTimeImmutable())->setTime(0, 0, 0),
            $this->ticket->status()->changeDate()
        );
    }

    /** @test */
    public function assignee(): void
    {
        self::assertEquals(new Assignee('accountId_!@#$', 'Display Full Name'), $this->ticket->assignee());
    }

    /** @test */
    public function customFields(): void
    {
        self::assertSame(['real_key' => 'customKey'], $this->ticket->customFields());
    }
}
