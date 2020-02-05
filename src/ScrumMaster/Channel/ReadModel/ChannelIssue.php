<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\ReadModel;

final class ChannelIssue
{
    private int $responseStatusCode;

    private ?string $displayName;

    public static function withStatusCode(int $responseStatusCode): self
    {
        return new self($responseStatusCode, null);
    }

    public static function withCodeAndAssignee(int $responseStatusCode, string $displayName): self
    {
        return new self($responseStatusCode, $displayName);
    }

    private function __construct(int $responseStatusCode, ?string $displayName)
    {
        $this->responseStatusCode = $responseStatusCode;
        $this->displayName = $displayName;
    }

    public function responseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function displayName(): ?string
    {
        return $this->displayName;
    }
}
