<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel;

use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use function count;

final class ChannelResult implements ChannelResultInterface
{
    /** @var array<string, ChannelIssue> */
    private $channelIssues = [];

    public static function withIssues(array $channelIssues): self
    {
        $self = new self();

        foreach ($channelIssues as $ticketKey => $channelIssue) {
            $self->addChannelIssue($ticketKey, $channelIssue);
        }

        return $self;
    }

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
