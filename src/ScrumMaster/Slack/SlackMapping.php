<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

/**
 * Map the JIRA name with the personal Slack ID
 */
final class SlackMapping
{
    /** @var array */
    private $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function toSlackId(?string $name): string
    {
        if (isset($this->ids[$name])) {
            return $this->ids[$name];
        }

        return $this->ids['fallback'];
    }
}
