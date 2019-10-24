<?php

declare(strict_types=1);

namespace App\Tests\ScrumMaster;

use App\ScrumMaster\JqlUrlBuilder;
use PHPUnit\Framework\TestCase;

final class JqlUrlBuilderTest extends TestCase
{
    /** @test */
    public function inOpenSprints(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints()',
            JqlUrlBuilder::inOpenSprints()->build()
        );
    }

    /** @test */
    public function forCoreServiceTeamProject(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ")',
            JqlUrlBuilder::inOpenSprints()->inProject('Core Service Team ')->build()
        );
    }

    /** @test */
    public function forReviewStatus(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ") AND status IN ("In Review")',
            JqlUrlBuilder::inOpenSprints()
                ->inProject('Core Service Team ')
                ->withStatus("In Review")
                ->build()
        );
    }

    /** @test */
    public function statusDidNotChangeSinceDays(): void
    {
        $this->assertEquals(
            'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND project IN ("Core Service Team ") AND status IN ("In Review") AND NOT status changed after -1d',
            JqlUrlBuilder::inOpenSprints()
                ->inProject('Core Service Team ')
                ->withStatus("In Review")
                ->statusDidNotChangeSinceDays(1)
                ->build()
        );
    }
}
