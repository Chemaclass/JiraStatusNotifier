<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\TicketsByAssignee;

interface ChannelInterface
{
    public function send(Company $company, TicketsByAssignee $ticketsByAssignee): ChannelResult;
}
