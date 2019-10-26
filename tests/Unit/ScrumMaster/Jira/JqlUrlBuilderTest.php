<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Jira;

use App\ScrumMaster\Jira\JqlUrlBuilder;
use App\ScrumMaster\Jira\ReadModel\Company;
use PHPUnit\Framework\TestCase;

final class JqlUrlBuilderTest extends TestCase
{
    /** @test */
    public function inOpenSprints(): void
    {
        $this->assertEquals(
            'https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints()',
            JqlUrlBuilder::inOpenSprints(
                Company::withName('company-name')
            )->build()
        );
    }

    /** @test */
    public function forCoreServiceTeamProject(): void
    {
        $this->assertEquals(
            'https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ")',
            JqlUrlBuilder::inOpenSprints(
                Company::withNameAndProject('company-name', 'Core Service Team ')
            )->build()
        );
    }

    /** @test */
    public function forReviewStatus(): void
    {
        $this->assertEquals(
            'https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND status IN ("In Review")',
            JqlUrlBuilder::inOpenSprints(Company::withName('company-name'))
                ->withStatus('In Review')
                ->build()
        );
    }

    /** @test */
    public function statusDidNotChangeSinceDays(): void
    {
        $this->assertEquals(
            'https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND NOT status changed after -1d',
            JqlUrlBuilder::inOpenSprints(Company::withName('company-name'))
                ->statusDidNotChangeSinceDays(1)
                ->build()
        );
    }

    /** @test */
    public function statusDidNotChangeSinceDaysAndStartSprintDate(): void
    {
        $this->assertEquals(
            'https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND status IN ("IN QA") AND ((status changed TO "IN QA" before 2019-10-14 AND NOT status changed after -4d) OR (status changed TO "IN QA" after 2019-10-14 AND NOT status changed after -2d))',
            JqlUrlBuilder::inOpenSprints(Company::withName('company-name'))
                ->withStatus('IN QA')
                ->statusDidNotChangeSinceDays(2, $startSprintDate = '2019-10-14')
                ->build()
        );
    }
}
