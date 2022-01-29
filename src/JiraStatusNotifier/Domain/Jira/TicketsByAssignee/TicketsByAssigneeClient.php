<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee;

use Chemaclass\JiraStatusNotifier\Domain\Jira\Board;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JqlUrlFactory;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\StrategyFilter\TicketFilter;

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
