<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\ChannelResultInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use function count;

final class SlackChannelResult implements ChannelResultInterface
{
    /** @var array<string, ChannelIssue> */
    private $channelIssues = [];

    public function addChannelIssue(string $ticketKey, ChannelIssue $channelIssue): self
    {
        $this->channelIssues[$ticketKey] = $channelIssue;

        return $this;
    }

    /** @return array<string, ChannelIssue> */
    public function channelIssues(): array
    {
        return $this->channelIssues;
    }

    public function total(): int
    {
        return count($this->channelIssues);
    }

    public function totalSuccessful(): int
    {
        return count(array_filter($this->channelIssues, function (ChannelIssue $channelIssue) {
            return 200 === $channelIssue->responseStatusCode();
        }));
    }

    public function totalFailed(): int
    {
        return count(array_filter($this->channelIssues, function (ChannelIssue $channelIssue) {
            return 200 !== $channelIssue->responseStatusCode();
        }));
    }

    public function append(self $other): void
    {
        foreach ($other->channelIssues() as $ticketKey => $channelIssue) {
            $this->addChannelIssue($ticketKey, $channelIssue);
        }
    }
}
