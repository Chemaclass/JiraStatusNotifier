<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\IO;

final class NotifierInput
{
    public const COMPANY_NAME = 'COMPANY_NAME';

    public const JIRA_PROJECT_NAME = 'JIRA_PROJECT_NAME';

    public const DAYS_FOR_STATUS = 'DAYS_FOR_STATUS';

    public const START_SPRINT_DATE = 'START_SPRINT_DATE';

    public const JIRA_USERS_TO_IGNORE = 'JIRA_USERS_TO_IGNORE';

    /** @var string */
    private $companyName;

    /** @var string */
    private $jiraProjectName;

    /** @var array */
    private $daysForStatus;

    /** @var string */
    private $startSpringDate;

    /** @var array */
    private $jiraUsersToIgnore;

    public static function new(
        string $companyName,
        string $jiraProjectName,
        array $daysForStatus,
        string $startSpringDate,
        array $jiraUsersToIgnore = []
    ): self {
        $self = new self();
        $self->companyName = $companyName;
        $self->jiraProjectName = $jiraProjectName;
        $self->daysForStatus = $daysForStatus;
        $self->startSpringDate = $startSpringDate;
        $self->jiraUsersToIgnore = $jiraUsersToIgnore;

        return $self;
    }

    private function __construct()
    {
    }

    public function companyName(): string
    {
        return $this->companyName;
    }

    public function jiraProjectName(): string
    {
        return $this->jiraProjectName;
    }

    public function daysForStatus(): array
    {
        return $this->daysForStatus;
    }

    public function jiraUsersToIgnore(): array
    {
        return $this->jiraUsersToIgnore;
    }

    public function startSpringDate(): string
    {
        return $this->startSpringDate;
    }
}
