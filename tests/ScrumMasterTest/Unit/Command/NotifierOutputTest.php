<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Command\NotifierOutput;
use Chemaclass\ScrumMaster\Slack\SlackNotifierResult;
use PHPUnit\Framework\TestCase;

final class NotifierOutputTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = new SlackNotifierResult();
        $result->addChannelIssue('K-1', ChannelIssue::withStatusCode(100));
        $result->addChannelIssue('K-2', ChannelIssue::withCodeAndAssignee(200, 'j.user.1'));
        $result->addChannelIssue('K-3', ChannelIssue::withStatusCode(300));
        $result->addChannelIssue('K-4', ChannelIssue::withCodeAndAssignee(100, 'j.user.2'));
        $result->addChannelIssue('K-5', ChannelIssue::withCodeAndAssignee(100, 'j.user.1'));

        $inMemoryOutput = new InMemoryOutput();
        (new NotifierOutput($inMemoryOutput))->write(['any channel name' => $result]);

        $lines = $inMemoryOutput->lines();
        $this->assertContains('# CHANNEL: any channel name', $lines);
        $this->assertContains('Total notifications: 5 (K-1, K-2: j.user.1, K-3, K-4: j.user.2, K-5: j.user.1)', $lines);
        $this->assertContains('Total successful notifications sent: 1 (K-2)', $lines);
        $this->assertContains('Total failed notifications sent: 4 (K-1, K-3, K-4, K-5)', $lines);
    }
}
