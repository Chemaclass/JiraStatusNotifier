<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\IgnoreStrategy;

use Chemaclass\JiraStatusNotifier\IgnoreStrategy\IgnoreStrategy;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class IgnoreStrategyTest extends TestCase
{
    /** @test */
    public function shouldIgnore(): void
    {
        $ignoreStrategy = IgnoreStrategy::byAssigneeKey('key1', 'key2');
        self::assertTrue($ignoreStrategy->shouldIgnoreTicket($this->newTicket('key1')));
    }

    /** @test */
    public function shouldNotIgnore(): void
    {
        $policy = IgnoreStrategy::byAssigneeKey('key1', 'key2');
        self::assertFalse($policy->shouldIgnoreTicket($this->newTicket('key3')));
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
