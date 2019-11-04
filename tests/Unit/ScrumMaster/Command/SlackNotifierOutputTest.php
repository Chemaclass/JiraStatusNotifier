<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierOutput;
use App\ScrumMaster\Slack\SlackNotifierResult;
use PHPUnit\Framework\TestCase;

final class SlackNotifierOutputTest extends TestCase
{
    /** @test */
    public function writeFromSlackNotifierOutput(): void
    {
        $result = new SlackNotifierResult();
        $result->addTicketKeyWithResponseCode('KEY-1', 100);
        $result->addTicketKeyWithResponseCode('KEY-2', 200);
        $result->addTicketKeyWithResponseCode('KEY-3', 300);
        $result->addTicketKeyWithResponseCode('KEY-4', 400);
        $result->addTicketKeyWithResponseCode('KEY-5', 500);

        $output = new InMemoryOutput();
        (new SlackNotifierOutput($output))->write($result);

        $this->assertContains('Total notifications: 5 (KEY-1, KEY-2, KEY-3, KEY-4, KEY-5)', $output->lines());
        $this->assertContains('Total successful notifications sent: 1', $output->lines());
        $this->assertContains('Total failed notifications sent: 4', $output->lines());
    }
}
