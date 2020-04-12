<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Channel\Slack;

use Chemaclass\JiraStatusNotifier\Channel\Slack\JiraMapping;
use PHPUnit\Framework\TestCase;

final class JiraMappingTest extends TestCase
{
    private JiraMapping $mapping;

    protected function setUp(): void
    {
        $this->mapping = JiraMapping::jiraNameWithSlackId([
            'fallback' => 'slack.group.id',
            'jira.person.id' => 'slack.person.id',
        ]);
    }

    /** @test */
    public function toSlackId(): void
    {
        $this->assertEquals('slack.person.id', $this->mapping->toSlackId('jira.person.id'));
    }

    /** @test */
    public function toSlackIdWhenJiraPersonIdNotFound(): void
    {
        $this->assertEquals('slack.group.id', $this->mapping->toSlackId('jira.unknown.id'));
    }
}
