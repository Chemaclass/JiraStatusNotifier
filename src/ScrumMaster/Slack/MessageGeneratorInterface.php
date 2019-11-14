<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Slack;

use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;

interface MessageGeneratorInterface
{
    public function forJiraTicket(JiraTicket $ticket, string $companyName): string;
}
