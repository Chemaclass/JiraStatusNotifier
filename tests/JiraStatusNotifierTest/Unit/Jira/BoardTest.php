<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira;

use Chemaclass\JiraStatusNotifier\Domain\Jira\Board;
use PHPUnit\Framework\TestCase;

final class BoardTest extends TestCase
{
    /**
     * @test
     */
    public function days_for_status(): void
    {
        $board = new Board(['status1' => 1, 'status2' => 2]);
        self::assertEquals(2, $board->getDaysForStatus('status2'));
    }

    /**
     * @test
     */
    public function days_for_status_when_status_not_found(): void
    {
        $board = new Board(['status1' => 1]);
        self::assertEquals(0, $board->getDaysForStatus('statusNotFound'));
    }
}
