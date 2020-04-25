<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\IgnoreStrategy;

use Chemaclass\JiraStatusNotifier\IgnoreStrategy\TicketIgnorer;
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
        $ignoreStrategy = TicketIgnorer::byAssigneeKey('key1', 'key2');
        self::assertTrue($ignoreStrategy->shouldIgnore($this->newTicket('key1')));
    }

    /** @test */
    public function shouldNotIgnore(): void
    {
        $policy = TicketIgnorer::byAssigneeKey('key1', 'key2');
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
