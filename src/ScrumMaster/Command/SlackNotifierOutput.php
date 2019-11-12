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
        $notificationTitles = $this->buildNotificationTitles($result);

        $this->output->writeln("Total notifications: {$result->total()} ($notificationTitles)");
        $this->output->writeln("Total successful notifications sent: {$result->totalSuccessful()}");
        $this->output->writeln("Total failed notifications sent: {$result->totalFailed()}");
    }

    private function buildNotificationTitles(SlackNotifierResult $result): string
    {
        $notificationTitles = [];

        foreach ($result->slackTickets() as $statusCode => $slackTicket) {
            $notificationTitles[] = null !== $slackTicket->displayName()
                ? "$statusCode: {$slackTicket->displayName()}"
                : $statusCode;
        }

        return implode(', ', $notificationTitles);
    }
}
