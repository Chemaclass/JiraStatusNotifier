<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier;

use Chemaclass\JiraStatusNotifier\Domain\IO\JiraConnectorInput;
use Gacela\Framework\AbstractConfig;

final class JiraStatusNotifierConfig extends AbstractConfig
{
    public function getJiraApiLabel(): string
    {
        return $this->get('JIRA_API_LABEL');
    }

    public function getJiraApiPassword(): string
    {
        return $this->get('JIRA_API_PASSWORD');
    }

    /**
     * @return array<string,string>
     */
    public function getCustomFields(): array
    {
        return ['customfield_10005' => 'StoryPoints'];
    }

    public function getJiraConnectorInput(): JiraConnectorInput
    {
        return (new JiraConnectorInput())
            ->setCompanyName($this->get(JiraConnectorInput::COMPANY_NAME))
            ->setJiraProjectName($this->get(JiraConnectorInput::JIRA_PROJECT_NAME))
            ->setDaysForStatus(json_decode($this->get(JiraConnectorInput::DAYS_FOR_STATUS), true))
            ->setJiraUsersToIgnore(json_decode($this->get(JiraConnectorInput::JIRA_USERS_TO_IGNORE), true));
    }

    public function getMailerUsername(): string
    {
        return $this->get('MAILER_USERNAME');
    }

    public function getMailerPassword(): string
    {
        return $this->get('MAILER_PASSWORD');
    }

    public function getJiraIdsToEmail(): array
    {
        return json_decode($this->get('JIRA_IDS_TO_EMAIL'), true);
    }

    public function getEmailTemplateName(): string
    {
        return $this->get('EMAIL_TEMPLATE_NAME', 'email-template.twig');
    }

    public function getSlackBotUserOauthAccessToken(): string
    {
        return $this->get('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN');
    }

    public function getSlackMappingIds(): array
    {
        return json_decode($this->get('SLACK_MAPPING_IDS'), true);
    }

    public function getSlackTemplateName(): string
    {
        return $this->get('SLACK_TEMPLATE_NAME', 'slack-template.twig');
    }

    public function getTemplatesDirectory(): string
    {
        return $this->get('TEMPLATES_DIRECTORY', $this->getAppRootDir() . '/../templates');
    }

    public function getOutputRendererTemplateName(): string
    {
        return $this->get('OUTPUT_RENDERER_TEMPLATE_NAME', 'output/cli-template.twig');
    }
}
