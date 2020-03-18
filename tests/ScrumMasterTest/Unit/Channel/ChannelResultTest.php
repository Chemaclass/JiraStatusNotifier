<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use PHPUnit\Framework\TestCase;

final class ChannelResultTest extends TestCase
{
    private ChannelResult $result;

    protected function setUp(): void
    {
        $this->result = (new ChannelResult())
            ->addChannelIssue('K-1', ChannelIssue::withStatusCode(100))
            ->addChannelIssue('K-2', ChannelIssue::withCodeAndAssignee(200, 'j.user.1'))
            ->addChannelIssue('K-3', ChannelIssue::withCodeAndAssignee(300, 'j.user.1'))
            ->addChannelIssue('K-4', ChannelIssue::withCodeAndAssignee(400, 'j.user.2'));
    }

    /** @test */
    public function total(): void
    {
        self::assertEquals(4, $this->result->total());
    }

    /** @test */
    public function totalFailed(): void
    {
        self::assertEquals(3, $this->result->totalFailed());
    }

    /** @test */
    public function totalSuccessful(): void
    {
        self::assertEquals(1, $this->result->totalSuccessful());
    }

    /** @test */
    public function channelIssues(): void
    {
        self::assertEquals(ChannelIssue::withStatusCode(100), $this->result->channelIssues()['K-1']);
        self::assertEquals(ChannelIssue::withCodeAndAssignee(200, 'j.user.1'), $this->result->channelIssues()['K-2']);
        self::assertEquals(ChannelIssue::withCodeAndAssignee(300, 'j.user.1'), $this->result->channelIssues()['K-3']);
        self::assertEquals(ChannelIssue::withCodeAndAssignee(400, 'j.user.2'), $this->result->channelIssues()['K-4']);
    }

    /** @test */
    public function ticketsAssignedToPeople(): void
    {
        self::assertEquals([
            'None' => ['K-1'],
            'j.user.1' => ['K-2', 'K-3'],
            'j.user.2' => ['K-4'],
        ], $this->result->ticketsAssignedToPeople());
    }
}
