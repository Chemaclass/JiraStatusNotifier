<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Slack;

/**
 * Map the JIRA name with the personal Slack ID
 */
final class JiraMapping
{
    private const FALLBACK_SLACK_ID_KEY = 'fallback';

    private const FALLBACK_SLACK_ID_VALUE = 'unknown';

    /** @var array<string,string> */
    private $ids;

    public static function jiraNameWithSlackId(array $ids): self
    {
        return new self($ids);
    }

    private function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function toSlackId(?string $name): string
    {
        if (isset($this->ids[$name])) {
            return $this->ids[$name];
        }

        return $this->ids[self::FALLBACK_SLACK_ID_KEY] ?? self::FALLBACK_SLACK_ID_VALUE;
    }
}
