<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel;

final class ChannelIssue
{
    private const DEFAULT_STATUS_CODE = 0;

    private int $responseStatusCode;

    private string $displayName;

    public static function withStatusCode(int $responseStatusCode): self
    {
        return new self($responseStatusCode, '');
    }

    public static function withAssignee(string $displayName): self
    {
        return new self(self::DEFAULT_STATUS_CODE, $displayName);
    }

    public static function withCodeAndAssignee(int $responseStatusCode, string $displayName): self
    {
        return new self($responseStatusCode, $displayName);
    }

    private function __construct(int $responseStatusCode, string $displayName)
    {
        $this->responseStatusCode = $responseStatusCode;
        $this->displayName = $displayName;
    }

    public function responseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }
}
