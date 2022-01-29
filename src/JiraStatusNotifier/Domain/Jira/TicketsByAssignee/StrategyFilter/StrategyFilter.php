<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;

interface StrategyFilter
{
    public function isValidTicket(JiraTicket $ticket): bool;
}
