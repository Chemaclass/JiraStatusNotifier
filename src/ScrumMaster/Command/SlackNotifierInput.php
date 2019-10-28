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

    /** @var string */
    private $jiraApiLabel;

    /** @var null|string */
    private $jiraApiPassword;

    /** @var null|string */
    private $slackBotUserOauthAccessToken;

    /** @var null|string */
    private $daysForStatus;

    /** @var null|string */
    private $slackMappingIds;

    public static function fromArray(array $array): self
    {
        $self = new self();
        $self->companyName = $array['COMPANY_NAME'] ?? null;
        $self->jiraProjectName = $array['JIRA_PROJECT_NAME'] ?? null;
        $self->jiraApiLabel = $array['JIRA_API_LABEL'] ?? null;
        $self->jiraApiPassword = $array['JIRA_API_PASSWORD'] ?? null;
        $self->slackBotUserOauthAccessToken = $array['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'] ?? null;
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

    public function jiraApiLabel(): string
    {
        if (!$this->jiraApiLabel) {
            throw new UndefinedParameter('JIRA_API_LABEL');
        }

        return $this->jiraApiLabel;
    }

    public function jiraApiPassword(): string
    {
        if (!$this->jiraApiPassword) {
            throw new UndefinedParameter('JIRA_API_PASSWORD');
        }

        return $this->jiraApiPassword;
    }

    public function slackBotUserOauthAccessToken(): string
    {
        if (!$this->slackBotUserOauthAccessToken) {
            throw new UndefinedParameter('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN');
        }

        return $this->slackBotUserOauthAccessToken;
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
