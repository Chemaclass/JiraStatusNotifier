<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;

interface ChannelInterface
{
    public function send(array $ticketsByAssignee, Company $company): ChannelResult;
}
