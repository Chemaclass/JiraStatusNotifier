<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\IO;

/** @psalm-immutable */
final class JiraConnectorInput
{
    public const COMPANY_NAME = 'COMPANY_NAME';

    public const JIRA_PROJECT_NAME = 'JIRA_PROJECT_NAME';

    public const DAYS_FOR_STATUS = 'DAYS_FOR_STATUS';

    public const JIRA_USERS_TO_IGNORE = 'JIRA_USERS_TO_IGNORE';

    private string $companyName;

    private string $jiraProjectName;

    /** @psalm-var array<string,int> */
    private array $daysForStatus;

    /** @psalm-var list<string> */
    private array $jiraUsersToIgnore;

    public static function new(
        string $companyName,
        string $jiraProjectName,
        array $daysForStatus,
        array $jiraUsersToIgnore = []
    ): self {
        return new self(
            $companyName,
            $jiraProjectName,
            $daysForStatus,
            $jiraUsersToIgnore
        );
    }

    private function __construct(
        string $companyName,
        string $jiraProjectName,
        array $daysForStatus,
        array $jiraUsersToIgnore = []
    ) {
        $this->companyName = $companyName;
        $this->jiraProjectName = $jiraProjectName;
        $this->daysForStatus = $daysForStatus;
        $this->jiraUsersToIgnore = $jiraUsersToIgnore;
    }

    public function companyName(): string
    {
        return $this->companyName;
    }

    public function jiraProjectName(): string
    {
        return $this->jiraProjectName;
    }

    /** @psalm-return array<string,int> */
    public function daysForStatus(): array
    {
        return $this->daysForStatus;
    }

    /** @psalm-return list<string> */
    public function jiraUsersToIgnore(): array
    {
        return $this->jiraUsersToIgnore;
    }
}
