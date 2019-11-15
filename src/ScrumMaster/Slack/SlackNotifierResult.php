<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Slack;

use Chemaclass\ScrumMaster\Slack\ReadModel\SlackTicket;
use function count;

final class SlackNotifierResult
{
    /** @var array<string, SlackTicket> */
    private $slackTickets = [];

    public function addSlackTicket(string $ticketKey, SlackTicket $slackTicket): void
    {
        $this->slackTickets[$ticketKey] = $slackTicket;
    }

    /** @return array<string, SlackTicket> */
    public function slackTickets(): array
    {
        return $this->slackTickets;
    }

    /** @return string[] */
    public function ticketKeys(): array
    {
        return array_keys($this->slackTickets());
    }

    public function total(): int
    {
        return count($this->slackTickets);
    }

    public function totalSuccessful(): int
    {
        return count(array_filter($this->slackTickets, function (SlackTicket $slackTicket) {
            return 200 === $slackTicket->responseStatusCode();
        }));
    }

    public function totalFailed(): int
    {
        return count(array_filter($this->slackTickets, function (SlackTicket $slackTicket) {
            return 200 !== $slackTicket->responseStatusCode();
        }));
    }

    public function append(self $other): void
    {
        foreach ($other->slackTickets() as $ticketKey => $slackTicket) {
            $this->addSlackTicket($ticketKey, $slackTicket);
        }
    }
}
