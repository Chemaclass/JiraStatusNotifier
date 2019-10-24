<?php

declare(strict_types=1);

namespace App\Tests\ScrumMaster;

use App\ScrumMaster\JiraUrlBuilder;
use PHPUnit\Framework\TestCase;

final class UrlBuilderTest extends TestCase
{
    /** @test */
    public function forNoSpecificProject(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints()',
            JiraUrlBuilder::inProject('')->build()
        );
    }

    /** @test */
    public function forCoreServiceTeamProject(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ")',
            JiraUrlBuilder::inProject('Core Service Team ')->build()
        );
    }

    /** @test */
    public function forReviewStatus(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ") AND status IN ("In Review")',
            JiraUrlBuilder::inProject('Core Service Team ')->withStatus("In Review")->build()
        );
    }

    /** @test */
    public function statusDidNotChangeSinceDays(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ") AND status IN ("In Review") AND NOT status changed after -1d',
            JiraUrlBuilder::inProject('Core Service Team ')
                ->withStatus("In Review")
                ->statusDidNotChangeSinceDays(1)
                ->build()
        );
    }

}
