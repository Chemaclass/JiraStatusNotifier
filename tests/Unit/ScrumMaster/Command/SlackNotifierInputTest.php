<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Tests\Unit\ScrumMaster\Command;

use Chemaclass\ScrumMaster\Command\SlackNotifierInput;
use PHPUnit\Framework\TestCase;

final class SlackNotifierInputTest extends TestCase
{
    /** @test */
    public function allParametersDefined(): void
    {
        $input = SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'JIRA_PROJECT_NAME' => 'project',
            'DAYS_FOR_STATUS' => '{"status":2}',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]);

        $this->assertEquals('company', $input->companyName());
        $this->assertEquals('project', $input->jiraProjectName());
        $this->assertEquals(['status' => 2], $input->daysForStatus());
        $this->assertEquals(['jira.id' => 'slack.id'], $input->slackMappingIds());
    }

    /** @test */
    public function undefinedCompanyName(): void
    {
        $this->expectExceptionMessage('Undefined parameter: COMPANY_NAME');

        SlackNotifierInput::fromArray([
            'JIRA_PROJECT_NAME' => 'project',
            'DAYS_FOR_STATUS' => '{"status":2}',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]);
    }

    /** @test */
    public function undefinedJiraProjectName(): void
    {
        $this->expectExceptionMessage('Undefined parameter: JIRA_PROJECT_NAME');

        SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'DAYS_FOR_STATUS' => '{"status":2}',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]);
    }

    /** @test */
    public function undefinedDaysForStatus(): void
    {
        $this->expectExceptionMessage('Undefined parameter: DAYS_FOR_STATUS');

        SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'JIRA_PROJECT_NAME' => 'project',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]);
    }

    /** @test */
    public function undefinedSlackMappingIds(): void
    {
        $this->expectExceptionMessage('Undefined parameter: SLACK_MAPPING_IDS');

        SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'JIRA_PROJECT_NAME' => 'project',
            'DAYS_FOR_STATUS' => '{"status":2}',
        ]);
    }
}
