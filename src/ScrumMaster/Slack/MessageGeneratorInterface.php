<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;

interface MessageGeneratorInterface
{
    public function forJiraTicket(JiraTicket $ticket, string $companyName): string;
}
