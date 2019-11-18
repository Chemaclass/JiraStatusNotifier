<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\ReadModel;

final class ChannelIssue
{
    /** @var int */
    private $responseStatusCode;

    /** @var null|string */
    private $displayName;

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
        $this->displayName = $displayName;
        $this->responseStatusCode = $responseStatusCode;
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