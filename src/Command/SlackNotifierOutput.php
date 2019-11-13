<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Command;

use Chemaclass\ScrumMaster\Command\IO\OutputInterface;
use Chemaclass\ScrumMaster\Slack\ReadModel\SlackTicket;
use Chemaclass\ScrumMaster\Slack\SlackNotifierResult;

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
        $notificationSuccessful = $this->buildNotificationSuccessful($result);
        $notificationFailed = $this->buildNotificationFailed($result);

        $this->output->writeln("Total notifications: {$result->total()} ($notificationTitles)");
        $this->output->writeln("Total successful notifications sent: {$result->totalSuccessful()} ($notificationSuccessful)");
        $this->output->writeln("Total failed notifications sent: {$result->totalFailed()} ($notificationFailed)");
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

    private function buildNotificationSuccessful(SlackNotifierResult $result): string
    {
        $notificationSuccessful = array_keys(array_filter($result->slackTickets(), function (SlackTicket $slackTicket) {
            return 200 === $slackTicket->responseStatusCode();
        }));

        return implode(', ', $notificationSuccessful);
    }

    private function buildNotificationFailed(SlackNotifierResult $result): string
    {
        $notificationFailed = array_keys(array_filter($result->slackTickets(), function (SlackTicket $slackTicket) {
            return 200 !== $slackTicket->responseStatusCode();
        }));

        return implode(', ', $notificationFailed);
    }
}
