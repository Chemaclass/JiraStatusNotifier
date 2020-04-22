<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira;

use Chemaclass\JiraStatusNotifier\Jira\Board;
use PHPUnit\Framework\TestCase;

final class BoardTest extends TestCase
{
    /** @test */
    public function daysForStatus(): void
    {
        $board = new Board(['status1' => 1, 'status2' => 2]);
        self::assertEquals(2, $board->getDaysForStatus('status2'));
    }

    /** @test */
    public function daysForStatusWhenStatusNotFound(): void
    {
        $board = new Board(['status1' => 1]);
        self::assertEquals(0, $board->getDaysForStatus('statusNotFound'));
    }
}
