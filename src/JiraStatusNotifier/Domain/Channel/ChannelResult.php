<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Channel;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use function count;

final class ChannelResult
{
    /** @var array<string, ChannelIssue> */
    private $channelIssues = [];

    /**
     * @param array<string, ChannelIssue> $channelIssues
     */
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

    /**
     * @return array<string, ChannelIssue>
     */
    public function channelIssues(): array
    {
        return $this->channelIssues;
    }

    /**
     * @return array<string, string[]>
     */
    public function ticketsAssignedToPeople(): array
    {
        $tickets = [];

        foreach ($this->peopleAssigned() as $people) {
            if (!isset($tickets[$people])) {
                $tickets[$people] = [];
            }
            /** @var ChannelIssue $issue */
            foreach ($this->channelIssues as $key => $issue) {
                if ($issue->displayName() === $people) {
                    $tickets[$people][] = $key;
                }
            }
        }

        $tickets['None'] = $tickets[''];
        unset($tickets['']);

        return $tickets;
    }

    public function total(): int
    {
        return count($this->channelIssues);
    }

    public function totalSuccessful(): int
    {
        return count(array_filter(
            $this->channelIssues,
            fn (ChannelIssue $issue): bool => 200 === $issue->responseStatusCode()
        ));
    }

    public function totalFailed(): int
    {
        return count(array_filter(
            $this->channelIssues,
            fn (ChannelIssue $issue): bool => 200 !== $issue->responseStatusCode()
        ));
    }

    /**
     * @return string[]
     */
    private function peopleAssigned(): array
    {
        $values = array_map(
            fn (ChannelIssue $issue): string => $issue->displayName(),
            $this->channelIssues
        );

        $people = array_values(array_unique($values));
        sort($people);

        return $people;
    }
}
