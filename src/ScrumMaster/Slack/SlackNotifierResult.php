<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Slack\ReadModel\SlackTicket;

final class SlackNotifierResult
{
    /** @var SlackTicket[] */
    private $slackTickets = [];

    public function addSlackTicket(SlackTicket $slackTicket): void
    {
        $this->slackTickets[] = $slackTicket;
    }

    /** @return SlackTicket[] */
    public function responseCodePerTickets(): array
    {
        return $this->slackTickets;
    }

    /** @return string[] */
    public function ticketKeys(): array
    {
        $keys = [];
        foreach ($this->slackTickets as $slackTicket) {
            $keys[] = $slackTicket->ticketCode();
        }

        return $keys;
    }

    public function total(): int
    {
        return count($this->slackTickets);
    }

    public function totalSuccessful(): int
    {
        return count(array_filter($this->slackTickets, function (SlackTicket $slackTicket) {
            return 200 === $slackTicket->statusCode();
        }));
    }

    public function totalFailed(): int
    {
        return count(array_filter($this->slackTickets, function (SlackTicket $slackTicket) {
            return 200 !== $slackTicket->statusCode();
        }));
    }

    public function append(self $other): void
    {
        foreach ($other->responseCodePerTickets() as $ticketKey => $responseCodeAndDisplayName) {
            $this->slackTickets[$ticketKey] = $responseCodeAndDisplayName;
        }
    }
}
