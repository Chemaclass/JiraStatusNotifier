<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

final class SlackNotifierResult
{
    /** @var array<string,int> */
    private $codesPerTickets = [];

    public function addTicketKeyWithResponseCode(string $ticketKey, int $statusCode): void
    {
        $this->codesPerTickets[$ticketKey] = $statusCode;
    }

    /** @return array<string,int> */
    public function responseCodePerTickets(): array
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

    public function totalSuccessful(): int
    {
        return count(array_filter($this->codesPerTickets, function ($statusCode) {
            return 200 === $statusCode;
        }));
    }

    public function totalFailed(): int
    {
        return count(array_filter($this->codesPerTickets, function ($statusCode) {
            return 200 !== $statusCode;
        }));
    }

    public function append(self $other): void
    {
        foreach ($other->responseCodePerTickets() as $ticketKey => $responseStatusCode) {
            $this->codesPerTickets[$ticketKey] = $responseStatusCode;
        }
    }
}
