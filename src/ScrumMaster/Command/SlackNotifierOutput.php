<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

use App\ScrumMaster\Command\IO\OutputInterface;
use App\ScrumMaster\Slack\SlackNotifierResult;

final class SlackNotifierOutput
{
    /** @var OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function write(SlackNotifierResult $result): void
    {
        $keys = implode(', ', $result->ticketKeys());
        $this->output->writeln("Total notifications: {$result->total()} ({$keys})");
        $this->output->writeln("Total successful notifications sent: {$result->totalSuccessful()}");
        $this->output->writeln("Total failed notifications sent: {$result->totalFailed()}");
    }
}
