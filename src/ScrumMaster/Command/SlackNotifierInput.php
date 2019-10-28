<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

use App\ScrumMaster\Command\Exception\UndefinedParameter;

final class SlackNotifierInput
{
    /** @var null|string */
    private $companyName;

    /** @var null|string */
    private $jiraProjectName;

    /** @var null|string */
    private $daysForStatus;

    /** @var null|string */
    private $slackMappingIds;

    public static function fromArray(array $array): self
    {
        $self = new self();
        $self->companyName = $array['COMPANY_NAME'] ?? null;
        $self->jiraProjectName = $array['JIRA_PROJECT_NAME'] ?? null;
        $self->daysForStatus = $array['DAYS_FOR_STATUS'] ?? null;
        $self->slackMappingIds = $array['SLACK_MAPPING_IDS'] ?? null;

        return $self;
    }

    private function __construct()
    {
    }

    public function companyName(): string
    {
        if (!$this->companyName) {
            throw new UndefinedParameter('COMPANY_NAME');
        }

        return $this->companyName;
    }

    public function jiraProjectName(): string
    {
        if (!$this->jiraProjectName) {
            throw new UndefinedParameter('JIRA_PROJECT_NAME');
        }

        return $this->jiraProjectName;
    }

    public function daysForStatus(): array
    {
        if (!$this->daysForStatus) {
            throw new UndefinedParameter('DAYS_FOR_STATUS');
        }

        return json_decode($this->daysForStatus, true);
    }

    public function slackMappingIds(): array
    {
        if (!$this->slackMappingIds) {
            throw new UndefinedParameter('SLACK_MAPPING_IDS');
        }

        return json_decode($this->slackMappingIds, true);
    }
}
