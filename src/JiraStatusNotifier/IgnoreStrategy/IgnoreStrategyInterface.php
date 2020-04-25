<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\IgnoreStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

interface IgnoreStrategyInterface
{
    public function shouldIgnore(JiraTicket $ticket): bool;
}
