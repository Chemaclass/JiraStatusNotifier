<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack\ReadModel;

final class SlackTicket
{
    /** @var string */
    private $ticketCode;

    /** @var string|null */
    private $displayName;

    /** @var int */
    private $statusCode;

    public function __construct(
        string $ticketCode,
        ?string $displayName,
        int $statusCode
    ) {
        $this->ticketCode = $ticketCode;
        $this->displayName = $displayName;
        $this->statusCode = $statusCode;
    }

    public function ticketCode(): string
    {
        return $this->ticketCode;
    }

    public function displayName(): ?string
    {
        return $this->displayName;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
