<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Slack\ReadModel;

final class SlackTicket
{
    /** @var null|string */
    private $displayName;

    /** @var int */
    private $responseStatusCode;

    public function __construct(
        ?string $displayName,
        int $responseStatusCode
    ) {
        $this->displayName = $displayName;
        $this->responseStatusCode = $responseStatusCode;
    }

    public function displayName(): ?string
    {
        return $this->displayName;
    }

    public function responseStatusCode(): int
    {
        return $this->responseStatusCode;
    }
}
