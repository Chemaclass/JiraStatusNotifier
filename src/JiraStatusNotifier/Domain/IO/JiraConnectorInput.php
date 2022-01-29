<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\IO;

final class JiraConnectorInput
{
    public const COMPANY_NAME = 'COMPANY_NAME';

    public const JIRA_PROJECT_NAME = 'JIRA_PROJECT_NAME';

    public const DAYS_FOR_STATUS = 'DAYS_FOR_STATUS';

    public const JIRA_USERS_TO_IGNORE = 'JIRA_USERS_TO_IGNORE';

    private string $companyName = '';

    private string $jiraProjectName = '';

    /** @psalm-var array<string,int> */
    private array $daysForStatus = [];

    /** @psalm-var list<string> */
    private array $jiraUsersToIgnore = [];

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getJiraProjectName(): string
    {
        return $this->jiraProjectName;
    }

    public function setJiraProjectName(string $jiraProjectName): self
    {
        $this->jiraProjectName = $jiraProjectName;
        return $this;
    }

    public function getDaysForStatus(): array
    {
        return $this->daysForStatus;
    }

    public function setDaysForStatus(array $daysForStatus): self
    {
        $this->daysForStatus = $daysForStatus;
        return $this;
    }

    public function getJiraUsersToIgnore(): array
    {
        return $this->jiraUsersToIgnore;
    }

    public function setJiraUsersToIgnore(array $jiraUsersToIgnore): self
    {
        $this->jiraUsersToIgnore = $jiraUsersToIgnore;
        return $this;
    }
}
