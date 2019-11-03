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
        $this->writeTotalNotifications($result);
        $this->writeSuccessfulAndFailedNotifications($result);
    }

    private function writeTotalNotifications(SlackNotifierResult $result): void
    {
        $keys = implode(', ', $result->ticketKeys());
        $this->output->writeln("Total notifications: {$result->total()} ({$keys})");
    }

    private function writeSuccessfulAndFailedNotifications(SlackNotifierResult $result): void
    {
        $totalSuccessful = $totalFailed = 0;

        foreach ($result->codesPerTickets() as $statusCode) {
            if (200 === $statusCode) {
                $totalSuccessful++;
            } else {
                $totalFailed++;
            }
        }

        $this->output->writeln("Total successful notifications sent: {$totalSuccessful}");
        $this->output->writeln("Total failed notifications sent: {$totalFailed}");
    }
}
