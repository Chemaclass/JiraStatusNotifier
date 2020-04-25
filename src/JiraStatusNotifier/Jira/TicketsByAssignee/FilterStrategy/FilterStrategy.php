<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\FilterStrategy;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

interface FilterStrategy
{
    public function isValidTicket(JiraTicket $ticket): bool;
}
