<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Jira;

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JqlUrlBuilder;
use App\ScrumMaster\Jira\JqlUrlFactory;
use App\ScrumMaster\Jira\ReadModel\Company;
use PHPUnit\Framework\TestCase;

final class JqlUrlFactoryTest extends TestCase
{
    /** @test */
    public function buildForAnExistingStatus(): void
    {
        $statusName = 'status';

        $factory = new JqlUrlFactory(
            new Board([$statusName => 2]),
            JqlUrlBuilder::inOpenSprints(Company::withName('company'))
        );

        $this->assertEquals(
            'https://company.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND status IN ("status") AND NOT status changed after -2d',
            $factory->buildUrl($statusName)
        );
    }

    /** @test */
    public function buildForAnUnknownStatus(): void
    {
        $factory = new JqlUrlFactory(
            new Board(['status' => 2], $fallbackValue = 99),
            JqlUrlBuilder::inOpenSprints(Company::withName('company'))
        );

        $this->assertEquals(
            'https://company.atlassian.net/rest/api/3/search?jql=sprint in openSprints() AND status IN ("unknown-status") AND NOT status changed after -99d',
            $factory->buildUrl('unknown-status')
        );
    }
}
