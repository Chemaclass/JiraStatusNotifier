<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use PHPUnit\Framework\TestCase;

final class ChannelResultTest extends TestCase
{
    /** @var ChannelResult */
    private $result;

    protected function setUp(): void
    {
        $this->result = (new ChannelResult())
            ->addChannelIssue('K-1', ChannelIssue::withStatusCode(100))
            ->addChannelIssue('K-2', ChannelIssue::withCodeAndAssignee(200, 'j.user.1'))
            ->addChannelIssue('K-3', ChannelIssue::withCodeAndAssignee(300, 'j.user.1'));
    }

    /** @test */
    public function total(): void
    {
        self::assertEquals(3, $this->result->total());
    }

    /** @test */
    public function totalFailed(): void
    {
        self::assertEquals(2, $this->result->totalFailed());
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
    }
}
