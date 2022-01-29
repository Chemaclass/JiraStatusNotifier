<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\IO;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Domain\Common\Request;

final class NotificationOutputExtractor
{
    private ChannelResult $result;

    public function __construct(ChannelResult $result)
    {
        $this->result = $result;
    }

    public function titles(): string
    {
        $notificationTitles = [];

        /** @var ChannelIssue $channelIssue */
        foreach ($this->result->channelIssues() as $statusCode => $channelIssue) {
            $notificationTitles[] = $channelIssue->displayName()
                ? "$statusCode: {$channelIssue->displayName()}"
                : $statusCode;
        }

        return implode(', ', $notificationTitles);
    }

    public function successful(): string
    {
        $notificationSuccessful = array_keys(array_filter(
            $this->result->channelIssues(),
            function (ChannelIssue $slackTicket) {
                return Request::HTTP_OK === $slackTicket->responseStatusCode();
            }
        ));

        return implode(', ', $notificationSuccessful);
    }

    public function failed(): string
    {
        $notificationFailed = array_keys(array_filter(
            $this->result->channelIssues(),
            function (ChannelIssue $slackTicket) {
                return Request::HTTP_OK !== $slackTicket->responseStatusCode();
            }
        ));

        return implode(', ', $notificationFailed);
    }
}
