<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Jira;

use Chemaclass\ScrumMaster\Jira\JqlUrlBuilder;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
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
    public function forAnyProject(): void
    {
        $expected = "https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints()";
        $expected .= " AND project IN ('AnyProject')";

        $this->assertEquals(
            $expected,
            JqlUrlBuilder::inOpenSprints(
                Company::withNameAndProject('company-name', 'AnyProject')
            )->build()
        );
    }

    /** @test */
    public function forReviewStatus(): void
    {
        $expected = "https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints()";
        $expected .= " AND status IN ('In Review')";

        $this->assertEquals(
            $expected,
            JqlUrlBuilder::inOpenSprints(Company::withName('company-name'))
                ->withStatus('In Review')
                ->build()
        );
    }

    /** @test */
    public function statusDidNotChangeSinceDays(): void
    {
        $expected = "https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints()";
        $expected .= " AND NOT status changed after -1d";

        $this->assertEquals(
            $expected,
            JqlUrlBuilder::inOpenSprints(Company::withName('company-name'))
                ->statusDidNotChangeSinceDays(1)
                ->build()
        );
    }

    /** @test */
    public function statusDidNotChangeSinceDaysAndStartSprintDate(): void
    {
        $expected = "https://company-name.atlassian.net/rest/api/3/search?jql=sprint in openSprints()";
        $expected .= " AND status IN ('IN QA')";
        $expected .= " AND ((status changed TO 'IN QA' before 2019-10-14 AND NOT status changed after -4d)";
        $expected .= " OR (status changed TO 'IN QA' after 2019-10-14 AND NOT status changed after -2d))";

        $this->assertEquals(
            $expected,
            JqlUrlBuilder::inOpenSprints(Company::withName('company-name'))
                ->withStatus('IN QA')
                ->statusDidNotChangeSinceDays(2, $startSprintDate = '2019-10-14')
                ->build()
        );
    }
}
