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
        $result->addChannelIssue('KEY-1', ChannelIssue::withStatusCode(100));
        $result->addChannelIssue('KEY-2', ChannelIssue::withCodeAndAssignee(200, 'jira.user.1'));
        $result->addChannelIssue('KEY-3', ChannelIssue::withStatusCode(300));
        $result->addChannelIssue('KEY-4', ChannelIssue::withCodeAndAssignee(100, 'jira.user.2'));
        $result->addChannelIssue('KEY-5', ChannelIssue::withCodeAndAssignee(100, 'jira.user.1'));

        $output = new InMemoryOutput();
        (new NotifierOutput($output))->write(['any channel name' => $result]);

        $this->assertContains('# CHANNEL: any channel name', $output->lines());
        $this->assertContains(
            'Total notifications: 5 (KEY-1, KEY-2: jira.user.1, KEY-3, KEY-4: jira.user.2, KEY-5: jira.user.1)',
            $output->lines()
        );
        $this->assertContains('Total successful notifications sent: 1 (KEY-2)', $output->lines());
        $this->assertContains('Total failed notifications sent: 4 (KEY-1, KEY-3, KEY-4, KEY-5)', $output->lines());
    }
}
