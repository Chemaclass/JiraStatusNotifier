<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierInput;
use PHPUnit\Framework\TestCase;

final class SlackNotifierInputTest extends TestCase
{
    /** @test */
    public function undefinedCompanyName(): void
    {
        $this->expectExceptionMessage('Undefined parameter: COMPANY_NAME');
        SlackNotifierInput::fromArray([])->companyName();
    }

    /** @test */
    public function definedCompanyName(): void
    {
        $this->assertEquals(
            'company',
            SlackNotifierInput::fromArray(['COMPANY_NAME' => 'company'])->companyName()
        );
    }

    /** @test */
    public function undefinedJiraProjectName(): void
    {
        $this->expectExceptionMessage('Undefined parameter: JIRA_PROJECT_NAME');
        SlackNotifierInput::fromArray([])->jiraProjectName();
    }

    /** @test */
    public function definedJiraProjectName(): void
    {
        $this->assertEquals(
            'project',
            SlackNotifierInput::fromArray(['JIRA_PROJECT_NAME' => 'project'])->jiraProjectName()
        );
    }

    /** @test */
    public function undefinedJiraApiLabel(): void
    {
        $this->expectExceptionMessage('Undefined parameter: JIRA_API_LABEL');
        SlackNotifierInput::fromArray([])->jiraApiLabel();
    }

    /** @test */
    public function definedJiraApiLabel(): void
    {
        $this->assertEquals(
            'label',
            SlackNotifierInput::fromArray(['JIRA_API_LABEL' => 'label'])->jiraApiLabel()
        );
    }

    /** @test */
    public function undefinedJiraApiPassword(): void
    {
        $this->expectExceptionMessage('Undefined parameter: JIRA_API_PASSWORD');
        SlackNotifierInput::fromArray([])->jiraApiPassword();
    }

    /** @test */
    public function definedJiraApiPassword(): void
    {
        $this->assertEquals(
            'passwd',
            SlackNotifierInput::fromArray(['JIRA_API_PASSWORD' => 'passwd'])->jiraApiPassword()
        );
    }

    /** @test */
    public function undefinedSlackBotUserOauthAccessToken(): void
    {
        $this->expectExceptionMessage('Undefined parameter: SLACK_BOT_USER_OAUTH_ACCESS_TOKEN');
        SlackNotifierInput::fromArray([])->slackBotUserOauthAccessToken();
    }

    /** @test */
    public function definedSlackBotUserOauthAccessToken(): void
    {
        $this->assertEquals(
            'label',
            SlackNotifierInput::fromArray(['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN' => 'label'])->slackBotUserOauthAccessToken()
        );
    }

    /** @test */
    public function undefinedDaysForStatus(): void
    {
        $this->expectExceptionMessage('Undefined parameter: DAYS_FOR_STATUS');
        SlackNotifierInput::fromArray([])->daysForStatus();
    }

    /** @test */
    public function definedDaysForStatus(): void
    {
        $this->assertEquals(
            ['status' => 2],
            SlackNotifierInput::fromArray(['DAYS_FOR_STATUS' => '{"status":2}'])->daysForStatus()
        );
    }

    /** @test */
    public function undefinedSlackMappingIds(): void
    {
        $this->expectExceptionMessage('Undefined parameter: SLACK_MAPPING_IDS');
        SlackNotifierInput::fromArray([])->slackMappingIds();
    }

    /** @test */
    public function definedSlackMappingIds(): void
    {
        $this->assertEquals(
            ['jira.id' => 'slack.id'],
            SlackNotifierInput::fromArray(['SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}'])->slackMappingIds()
        );
    }
}
