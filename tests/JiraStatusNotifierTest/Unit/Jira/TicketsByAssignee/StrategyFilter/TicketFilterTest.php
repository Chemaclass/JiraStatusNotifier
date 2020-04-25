<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\TicketStatus;
use Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\StrategyFilter\TicketFilter;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TicketFilterTest extends TestCase
{
    /** @test */
    public function shouldIgnore(): void
    {
        $ignoreStrategy = TicketFilter::notWithAssigneeKeys('key1', 'key2');
        self::assertTrue($ignoreStrategy->shouldIgnore($this->newTicket('key1')));
    }

    /** @test */
    public function shouldNotIgnore(): void
    {
        $policy = TicketFilter::notWithAssigneeKeys('key1', 'key2');
        self::assertFalse($policy->shouldIgnore($this->newTicket('key3')));
    }

    private function newTicket(string $key): JiraTicket
    {
        return new JiraTicket(
            'The title',
            'KEY-N',
            new TicketStatus('In Progress', new DateTimeImmutable()),
            new Assignee(
                'assignee.name',
                $key,
                'Full Name',
                'any@example.com'
            )
        );
    }
}
