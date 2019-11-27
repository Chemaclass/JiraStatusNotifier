<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Jira;

use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JqlUrlBuilder;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use PHPUnit\Framework\TestCase;

final class JqlUrlFactoryTest extends TestCase
{
    /** @test */
    public function buildForAnExistingStatus(): void
    {
        $factory = new JqlUrlFactory(
            new Board(['statusName' => 2]),
            JqlUrlBuilder::inOpenSprints(Company::withName('company'))
        );

        $expected = 'https://company.atlassian.net/rest/api/3/search?jql=sprint in openSprints()';
        $expected .= " AND status IN ('statusName')";
        $expected .= ' AND NOT status changed after -2d';
        $this->assertEquals($expected, $factory->buildUrl('statusName'));
    }

    /** @test */
    public function buildForAnUnknownStatus(): void
    {
        $factory = new JqlUrlFactory(
            new Board(['status' => 2], $fallbackValue = 99),
            JqlUrlBuilder::inOpenSprints(Company::withName('company'))
        );

        $expected = 'https://company.atlassian.net/rest/api/3/search?jql=sprint in openSprints()';
        $expected .= " AND status IN ('unknown-status')";
        $expected .= ' AND NOT status changed after -99d';
        $this->assertEquals($expected, $factory->buildUrl('unknown-status'));
    }
}
