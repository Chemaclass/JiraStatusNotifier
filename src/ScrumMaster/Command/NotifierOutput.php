<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Command;

use Chemaclass\ScrumMaster\Channel\ChannelResultInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Command\IO\OutputInterface;

final class NotifierOutput
{
    public const HTTP_OK = 200;

    /** @var OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param array<string,ChannelResultInterface> $results */
    public function write(array $results): void
    {
        foreach ($results as $channelName => $result) {
            $this->writeChannel($channelName, $result);
        }
    }

    private function writeChannel(string $name, ChannelResultInterface $result): void
    {
        $this->output->writeln("# CHANNEL: {$name}");
        $notificationTitles = $this->buildNotificationTitles($result);
        $notificationSuccessful = $this->buildNotificationSuccessful($result);
        $notificationFailed = $this->buildNotificationFailed($result);

        $this->output->writeln("Total notifications: {$result->total()} ($notificationTitles)");
        $this->output->writeln("Total successful notifications sent: {$result->totalSuccessful()} ($notificationSuccessful)");
        $this->output->writeln("Total failed notifications sent: {$result->totalFailed()} ($notificationFailed)");
    }

    private function buildNotificationTitles(ChannelResultInterface $result): string
    {
        $notificationTitles = [];

        /** @var ChannelIssue $channelIssue */
        foreach ($result->channelIssues() as $statusCode => $channelIssue) {
            $notificationTitles[] = (null !== $channelIssue->displayName())
                ? "$statusCode: {$channelIssue->displayName()}"
                : $statusCode;
        }

        return implode(', ', $notificationTitles);
    }

    private function buildNotificationSuccessful(ChannelResultInterface $result): string
    {
        $notificationSuccessful = array_keys(array_filter(
            $result->channelIssues(),
            function (ChannelIssue $slackTicket) {
                return self::HTTP_OK === $slackTicket->responseStatusCode();
            }
        ));

        return implode(', ', $notificationSuccessful);
    }

    private function buildNotificationFailed(ChannelResultInterface $result): string
    {
        $notificationFailed = array_keys(array_filter(
            $result->channelIssues(),
            function (ChannelIssue $slackTicket) {
                return self::HTTP_OK !== $slackTicket->responseStatusCode();
            }
        ));

        return implode(', ', $notificationFailed);
    }
}
