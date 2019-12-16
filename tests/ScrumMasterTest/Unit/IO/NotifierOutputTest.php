<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\IO;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\IO\NotifierOutput;
use Chemaclass\ScrumMaster\IO\OutputInterface;
use PHPUnit\Framework\TestCase;

final class NotifierOutputTest extends TestCase
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

        $inMemoryOutput = $this->inMemoryOutput();
        (new NotifierOutput($inMemoryOutput))->write(['any channel name' => $result]);
        $lines = $inMemoryOutput->lines();

        $this->assertContains('# CHANNEL: any channel name', $lines);
        $this->assertContains('Total notifications: 5 (K-1, K-2: j.user.1, K-3, K-4: j.user.2, K-5: j.user.1)', $lines);
        $this->assertContains('Total successful notifications sent: 1 (K-2)', $lines);
        $this->assertContains('Total failed notifications sent: 4 (K-1, K-3, K-4, K-5)', $lines);
    }

    private function inMemoryOutput(): OutputInterface
    {
        return new class() implements OutputInterface {
            /** @var array */
            private $lines = [];

            public function write(string $text): void
            {
                $this->lines[] = $text;
            }

            public function writeln(string $text): void
            {
                $this->lines[] = $text;
            }

            public function lines(): array
            {
                return $this->lines;
            }
        };
    }
}
