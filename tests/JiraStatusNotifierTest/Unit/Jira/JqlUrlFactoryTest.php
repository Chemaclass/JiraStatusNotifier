<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Jira;

use Chemaclass\JiraStatusNotifier\Jira\Board;
use Chemaclass\JiraStatusNotifier\Jira\JqlUrlBuilder;
use Chemaclass\JiraStatusNotifier\Jira\JqlUrlFactory;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;
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
}
