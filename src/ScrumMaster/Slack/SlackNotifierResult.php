<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Slack\ReadModel\SlackTicket;

final class SlackNotifierResult
{
    /** @var array<string, SlackTicket> */
    private $slackTickets = [];

    public function addSlackTicket(SlackTicket $slackTicket): void
    {
        $this->slackTickets[$slackTicket->ticketCode()] = $slackTicket;
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
        foreach ($other->slackTickets() as $slackTicket) {
            $this->addSlackTicket($slackTicket);
        }
    }
}
