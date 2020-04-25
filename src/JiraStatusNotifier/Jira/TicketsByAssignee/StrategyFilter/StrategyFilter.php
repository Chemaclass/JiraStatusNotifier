<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\StrategyFilter;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

interface StrategyFilter
{
    public function isValidTicket(JiraTicket $ticket): bool;
}
