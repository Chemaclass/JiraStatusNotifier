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
