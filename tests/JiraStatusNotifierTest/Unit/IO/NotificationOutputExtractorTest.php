<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\IO;

use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\IO\NotificationOutputExtractor;
use PHPUnit\Framework\TestCase;

final class NotificationOutputExtractorTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = (new ChannelResult())
            ->addChannelIssue('K-1', ChannelIssue::withStatusCode(100))
            ->addChannelIssue('K-2', ChannelIssue::withCodeAndAssignee(200, 'j.user.1'))
            ->addChannelIssue('K-3', ChannelIssue::withStatusCode(300))
            ->addChannelIssue('K-4', ChannelIssue::withCodeAndAssignee(100, 'j.user.2'))
            ->addChannelIssue('K-5', ChannelIssue::withCodeAndAssignee(100, 'j.user.1'));

        $outputExtractor = new NotificationOutputExtractor($result);
        $this->assertEquals('K-1, K-2: j.user.1, K-3, K-4: j.user.2, K-5: j.user.1', $outputExtractor->titles());
        $this->assertEquals('K-2', $outputExtractor->successful());
        $this->assertEquals('K-1, K-3, K-4, K-5', $outputExtractor->failed());
    }
}
