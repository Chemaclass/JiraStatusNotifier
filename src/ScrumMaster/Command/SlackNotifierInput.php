<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

final class SlackNotifierInput
{
    /** @var string */
    private $companyName;

    /** @var string */
    private $jiraProjectName;

    /** @var string */
    private $jiraApiLabel;

    /** @var string */
    private $jiraApiPassword;

    /** @var string */
    private $slackBotUserOauthAccessToken;

    /** @var array */
    private $daysForStatus;

    /** @var array */
    private $slackMappingIds;

    public static function fromArray(array $array): self
    {
        $self = new self();
        $self->companyName = $array['COMPANY_NAME'];
        $self->jiraProjectName = $array['JIRA_PROJECT_NAME'];
        $self->jiraApiLabel = $array['JIRA_API_LABEL'];
        $self->jiraApiPassword = $array['JIRA_API_PASSWORD'];
        $self->slackBotUserOauthAccessToken = $array['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'];
        $self->daysForStatus = json_decode($array['DAYS_FOR_STATUS'], true);
        $self->slackMappingIds = json_decode($array['SLACK_MAPPING_IDS'], true);

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

    public function jiraApiLabel(): string
    {
        return $this->jiraApiLabel;
    }

    public function jiraApiPassword(): string
    {
        return $this->jiraApiPassword;
    }

    public function slackBotUserOauthAccessToken(): string
    {
        return $this->slackBotUserOauthAccessToken;
    }

    public function daysForStatus(): array
    {
        return $this->daysForStatus;
    }

    public function slackMappingIds(): array
    {
        return $this->slackMappingIds;
    }
}
