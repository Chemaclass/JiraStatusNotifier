<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\TicketStatus;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\StrategyFilter\TicketFilter;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TicketFilterTest extends TestCase
{
    /**
     * @test
     */
    public function should_ignore(): void
    {
        $filter = TicketFilter::notWithAssigneeKeys('key1', 'key2');
        self::assertTrue($filter->shouldIgnore($this->newTicket('key1')));
    }

    /**
     * @test
     */
    public function should_not_ignore(): void
    {
        $filter = TicketFilter::notWithAssigneeKeys('key1', 'key2');
        self::assertFalse($filter->shouldIgnore($this->newTicket('key3')));
    }

    private function newTicket(string $accountId): JiraTicket
    {
        return new JiraTicket(
            'The title',
            'KEY-N',
            new TicketStatus('In Progress', new DateTimeImmutable()),
            new Assignee($accountId, 'Display Full Name')
        );
    }
}
