<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Jira;

use Chemaclass\ScrumMaster\Jira\Board;
use PHPUnit\Framework\TestCase;

final class BoardTest extends TestCase
{
    /** @test */
    public function daysForStatus(): void
    {
        $board = new Board(['status1' => 1, 'status2' => 2]);
        $this->assertEquals(2, $board->getDaysForStatus('status2'));
        $this->assertEquals(1, $board->getDaysForStatus('statusNotFound'));
    }

    /** @test */
    public function daysForStatusWhenStatusNotFound(): void
    {
        $board = new Board(['status1' => 1], $default = 9);
        $this->assertEquals($default, $board->getDaysForStatus('statusNotFound'));
    }
}
