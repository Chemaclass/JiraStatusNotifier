<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

use App\ScrumMaster\Command\Exception\UndefinedParameter;

final class SlackNotifierInput
{
    public const COMPANY_NAME = 'COMPANY_NAME';

    public const JIRA_PROJECT_NAME = 'JIRA_PROJECT_NAME';

    public const DAYS_FOR_STATUS = 'DAYS_FOR_STATUS';

    public const SLACK_MAPPING_IDS = 'SLACK_MAPPING_IDS';

    private const MANDATORY_PARAMETERS = [
        self::COMPANY_NAME,
        self::JIRA_PROJECT_NAME,
        self::DAYS_FOR_STATUS,
        self::SLACK_MAPPING_IDS,
    ];

    /** @var string */
    private $companyName;

    /** @var string */
    private $jiraProjectName;

    /** @var string */
    private $daysForStatus;

    /** @var string */
    private $slackMappingIds;

    public static function fromArray(array $array): self
    {
        static::validateKeys($array);

        $self = new self();
        $self->companyName = $array[self::COMPANY_NAME];
        $self->jiraProjectName = $array[self::JIRA_PROJECT_NAME];
        $self->daysForStatus = $array[self::DAYS_FOR_STATUS];
        $self->slackMappingIds = $array[self::SLACK_MAPPING_IDS];

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
        return json_decode($this->daysForStatus, true);
    }

    public function slackMappingIds(): array
    {
        return json_decode($this->slackMappingIds, true);
    }

    private static function validateKeys(array $params): void
    {
        foreach (self::MANDATORY_PARAMETERS as $name) {
            if (!isset($params[$name])) {
                throw new UndefinedParameter($name);
            }
        }
    }
}
