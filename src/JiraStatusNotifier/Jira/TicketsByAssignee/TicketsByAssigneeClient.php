<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee;

use Chemaclass\JiraStatusNotifier\Jira\Board;
use Chemaclass\JiraStatusNotifier\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Jira\JqlUrlFactory;
use Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\FilterStrategy\TicketFilter;

final class TicketsByAssigneeClient
{
    private JiraHttpClient $jiraClient;

    private JqlUrlFactory $jqlUrlFactory;

    private TicketFilter $ignoreTickets;

    public function __construct(
        JiraHttpClient $jiraClient,
        JqlUrlFactory $jqlUrlFactory,
        TicketFilter $ignoreTickets
    ) {
        $this->jiraClient = $jiraClient;
        $this->jqlUrlFactory = $jqlUrlFactory;
        $this->ignoreTickets = $ignoreTickets;
    }

    public function fetchFromBoard(Board $board): TicketsByAssignee
    {
        $ticketsByAssignee = new TicketsByAssignee();

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($this->jqlUrlFactory, $statusName);

            foreach ($tickets as $ticket) {
                if ($this->ignoreTickets->shouldIgnore($ticket)) {
                    continue;
                }

                $ticketsByAssignee->add($ticket);
            }
        }

        return $ticketsByAssignee;
    }
}
