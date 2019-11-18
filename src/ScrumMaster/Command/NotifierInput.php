<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Command;

use Chemaclass\ScrumMaster\Command\Exception\UndefinedParameter;
use function count;
use function json_decode;

final class NotifierInput
{
    public const COMPANY_NAME = 'COMPANY_NAME';

    public const JIRA_PROJECT_NAME = 'JIRA_PROJECT_NAME';

    public const DAYS_FOR_STATUS = 'DAYS_FOR_STATUS';

    public const JIRA_USERS_TO_IGNORE = 'JIRA_USERS_TO_IGNORE';

    private const MANDATORY_PARAMETERS = [
        self::COMPANY_NAME,
        self::JIRA_PROJECT_NAME,
        self::DAYS_FOR_STATUS,
    ];

    /** @var string */
    private $companyName;

    /** @var string */
    private $jiraProjectName;

    /** @var array */
    private $daysForStatus;

    /** @var array */
    private $jiraUsersToIgnore;

    public static function fromArray(array $params): self
    {
        static::validateKeys($params);

        $self = new self();
        $self->companyName = $params[self::COMPANY_NAME];
        $self->jiraProjectName = $params[self::JIRA_PROJECT_NAME];
        $self->daysForStatus = json_decode($params[self::DAYS_FOR_STATUS], true);
        $self->jiraUsersToIgnore = isset($params[self::JIRA_USERS_TO_IGNORE])
            ? json_decode($params[self::JIRA_USERS_TO_IGNORE], true)
            : [];

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

    private static function validateKeys(array $params): void
    {
        $errors = [];

        foreach (self::MANDATORY_PARAMETERS as $name) {
            if (!isset($params[$name])) {
                $errors[] = $name;
            }
        }

        if (count($errors) > 0) {
            throw new UndefinedParameter($errors);
        }
    }
}
