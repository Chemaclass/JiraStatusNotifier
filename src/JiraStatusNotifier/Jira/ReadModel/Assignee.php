<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\ReadModel;

final class Assignee
{
    private string $accountId;

    private string $displayName;

    public static function empty(): self
    {
        return new self(
            $accountId = '',
            $displayName = ''
        );
    }

    public function __construct(
        string $accountId,
        string $displayName
    ) {
        $this->accountId = $accountId;
        $this->displayName = $displayName;
    }

    public function accountId(): string
    {
        return $this->accountId;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }
}
