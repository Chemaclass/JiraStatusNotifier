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
        $this->assertEquals(2, $board->getDaysForStatus('status2'));
        $this->assertEquals(Board::FALLBACK_VALUE_DEFAULT, $board->getDaysForStatus('statusNotFound'));
    }

    /** @test */
    public function daysForStatusWhenStatusNotFound(): void
    {
        $fallbackValue = 9999;
        $board = new Board(['status1' => 1], $fallbackValue);
        $this->assertEquals($fallbackValue, $board->getDaysForStatus('statusNotFound'));
    }
}
