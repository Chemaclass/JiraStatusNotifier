<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

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
        ]);

        $this->assertEquals('company', $input->companyName());
        $this->assertEquals('project', $input->jiraProjectName());
        $this->assertEquals(['status' => 2], $input->daysForStatus());
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
}
