<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Slack;

use App\ScrumMaster\Slack\SlackMapping;
use PHPUnit\Framework\TestCase;

final class SlackMappingTest extends TestCase
{
    /** @var SlackMapping */
    private $mapping;

    protected function setUp(): void
    {
        $this->mapping = new SlackMapping([
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
