<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel;

use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;

interface MessageGeneratorInterface
{
    /**
     * @param JiraTicket[] $tickets
     */
    public function forJiraTickets(array $tickets, string $companyName): string;
}
