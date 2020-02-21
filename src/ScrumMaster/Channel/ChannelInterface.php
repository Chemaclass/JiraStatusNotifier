<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel;

use Chemaclass\ScrumMaster\Jira\ReadModel\Company;

interface ChannelInterface
{
    public function sendNotifications(array $ticketsByAssignee, Company $company): ChannelResult;
}
