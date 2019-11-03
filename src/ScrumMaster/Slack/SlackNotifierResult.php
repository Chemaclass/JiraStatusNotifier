<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

final class SlackNotifierResult
{
    /** @var array<string,int> */
    private $codesPerTickets = [];

    public function addTicketWithResponseCode(string $ticketKey, int $statusCode): void
    {
        $this->codesPerTickets[$ticketKey] = $statusCode;
    }

    /** @return array<string,int> */
    public function codesPerTickets(): array
    {
        return $this->codesPerTickets;
    }

    /** @return string[] */
    public function ticketKeys(): array
    {
        return array_keys($this->codesPerTickets);
    }

    public function total(): int
    {
        return count($this->codesPerTickets);
    }
}
