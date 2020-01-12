<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\IO;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Common\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class NotifierOutput
{
    /** @var OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param array<string,ChannelResult> $results */
    public function write(array $results): void
    {
        foreach ($results as $channelName => $result) {
            $this->writeChannel($channelName, $result);
        }
    }

    private function writeChannel(string $name, ChannelResult $result): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader);

        $notificationTitles = $this->buildNotificationTitles($result);
        $notificationSuccessful = $this->buildNotificationSuccessful($result);
        $notificationFailed = $this->buildNotificationFailed($result);

        $render = $twig->render('ticket_status.twig', [
            'name' => $name,
            'result' => $result,
            'notificationTitles' => $notificationTitles,
            'notificationSucessful' => $notificationSuccessful,
            'notificationFailed' => $notificationFailed,
        ]);

        $lines = explode("\n", $render);
        foreach ($lines as $line) {
            $this->output->writeln($line);
        }
    }

    private function buildNotificationTitles(ChannelResult $result): string
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

    private function buildNotificationSuccessful(ChannelResult $result): string
    {
        $notificationSuccessful = array_keys(array_filter(
            $result->channelIssues(),
            function (ChannelIssue $slackTicket) {
                return Request::HTTP_OK === $slackTicket->responseStatusCode();
            }
        ));

        return implode(', ', $notificationSuccessful);
    }

    private function buildNotificationFailed(ChannelResult $result): string
    {
        $notificationFailed = array_keys(array_filter(
            $result->channelIssues(),
            function (ChannelIssue $slackTicket) {
                return Request::HTTP_OK !== $slackTicket->responseStatusCode();
            }
        ));

        return implode(', ', $notificationFailed);
    }
}
