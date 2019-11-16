<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Command\SlackNotifierOutput;
use Chemaclass\ScrumMaster\Slack\ReadModel\SlackTicket;
use Chemaclass\ScrumMaster\Slack\SlackNotifierResult;
use PHPUnit\Framework\TestCase;

final class SlackNotifierOutputTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = new SlackNotifierResult();
        $result->addSlackTicket('KEY-1', SlackTicket::withStatusCode(100));
        $result->addSlackTicket('KEY-2', SlackTicket::withStatusCodeAndAssignee(200, 'jira.user.1'));
        $result->addSlackTicket('KEY-3', SlackTicket::withStatusCode(300));
        $result->addSlackTicket('KEY-4', SlackTicket::withStatusCodeAndAssignee(100, 'jira.user.2'));
        $result->addSlackTicket('KEY-5', SlackTicket::withStatusCodeAndAssignee(100, 'jira.user.1'));

        $output = new InMemoryOutput();
        (new SlackNotifierOutput($output))->write($result);

        $this->assertContains(
            'Total notifications: 5 (KEY-1, KEY-2: jira.user.1, KEY-3, KEY-4: jira.user.2, KEY-5: jira.user.1)',
            $output->lines()
        );
        $this->assertContains('Total successful notifications sent: 1 (KEY-2)', $output->lines());
        $this->assertContains('Total failed notifications sent: 4 (KEY-1, KEY-3, KEY-4, KEY-5)', $output->lines());
    }
}
