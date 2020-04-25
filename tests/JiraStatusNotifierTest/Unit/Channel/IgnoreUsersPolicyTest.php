<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Channel;

use Chemaclass\JiraStatusNotifier\Channel\IgnoreUsersPolicy;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class IgnoreUsersPolicyTest extends TestCase
{
    /** @test */
    public function shouldIgnore(): void
    {
        $policy = new IgnoreUsersPolicy('assigneeKey1', 'assigneeKey2');
        self::assertTrue($policy->shouldIgnore($this->newTicket('assigneeKey1')));
    }

    /** @test */
    public function shouldNotIgnore(): void
    {
        $policy = new IgnoreUsersPolicy('assigneeKey1', 'assigneeKey2');
        self::assertFalse($policy->shouldIgnore($this->newTicket('assigneeKey3')));
    }

    private function newTicket(string $assigneeKey): JiraTicket
    {
        return new JiraTicket(
            'The title',
            'KEY-N',
            new TicketStatus('In Progress', new DateTimeImmutable()),
            new Assignee(
                'assignee.name',
                $assigneeKey,
                'Full Name',
                'any@example.com'
            )
        );
    }
}
