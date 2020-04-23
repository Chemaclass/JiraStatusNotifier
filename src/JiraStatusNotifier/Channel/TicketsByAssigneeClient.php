<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel;

use Chemaclass\JiraStatusNotifier\Jira\Board;
use Chemaclass\JiraStatusNotifier\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Jira\JqlUrlFactory;
use function in_array;

final class TicketsByAssigneeClient
{
    private JiraHttpClient $jiraClient;

    private JqlUrlFactory $jqlUrlFactory;

    private array $jiraUsersToIgnore;

    public function __construct(
        JiraHttpClient $jiraClient,
        JqlUrlFactory $jqlUrlFactory,
        array $jiraUsersToIgnore
    ) {
        $this->jiraClient = $jiraClient;
        $this->jqlUrlFactory = $jqlUrlFactory;
        $this->jiraUsersToIgnore = $jiraUsersToIgnore;
    }

    public function fetchFromBoard(Board $board): TicketsByAssignee
    {
        $ticketsByAssignee = new TicketsByAssignee();

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($this->jqlUrlFactory, $statusName);

            foreach ($tickets as $ticket) {
                if (in_array($ticket->assignee()->key(), $this->jiraUsersToIgnore)) {
                    continue;
                }

                $ticketsByAssignee->add($ticket);
            }
        }

        return $ticketsByAssignee;
    }
}
