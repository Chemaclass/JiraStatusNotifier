<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Channel;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\TicketsByAssignee;

interface ChannelInterface
{
    public function send(Company $company, TicketsByAssignee $ticketsByAssignee): ChannelResult;
}
