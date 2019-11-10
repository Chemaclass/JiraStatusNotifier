<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierOutput;
use App\ScrumMaster\Slack\ReadModel\SlackTicket;
use App\ScrumMaster\Slack\SlackNotifierResult;
use PHPUnit\Framework\TestCase;

final class SlackNotifierOutputTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = new SlackNotifierResult();
        $result->addSlackTicket(new SlackTicket('KEY-1', null, 100));
        $result->addSlackTicket(new SlackTicket('KEY-2', 'jira.user.1', 200));
        $result->addSlackTicket(new SlackTicket('KEY-3', null, 300));
        $result->addSlackTicket(new SlackTicket('KEY-4', 'jira.user.2', 400));
        $result->addSlackTicket(new SlackTicket('KEY-5', 'jira.user.1', 500));

        $output = new InMemoryOutput();
        (new SlackNotifierOutput($output))->write($result);

        $this->assertContains('Total notifications: 5 (KEY-1, KEY-2: jira.user.1, KEY-3, KEY-4: jira.user.2, KEY-5: jira.user.1)', $output->lines());
        $this->assertContains('Total successful notifications sent: 1', $output->lines());
        $this->assertContains('Total failed notifications sent: 4', $output->lines());
    }
}
