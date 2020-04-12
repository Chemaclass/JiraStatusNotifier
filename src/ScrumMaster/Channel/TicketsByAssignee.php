<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel;

use function array_merge;
use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use function in_array;

final class TicketsByAssignee
{
    private JiraHttpClient $jiraClient;

    private JqlUrlFactory $jqlUrlFactory;

    /** @var array */
    private $jiraUsersToIgnore;

    public function __construct(
        JiraHttpClient $jiraClient,
        JqlUrlFactory $jqlUrlFactory,
        array $jiraUsersToIgnore
    ) {
        $this->jiraClient = $jiraClient;
        $this->jqlUrlFactory = $jqlUrlFactory;
        $this->jiraUsersToIgnore = $jiraUsersToIgnore;
    }

    /** @psalm-return array<string, array<array-key, JiraTicket>> */
    public function fetchFromBoard(Board $board): array
    {
        $ticketsByAssignee = [];

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $grouped = $this->ticketsByAssigneeInStatus($statusName);

            foreach ($grouped as $assigneeKey => $tickets) {
                if (!isset($ticketsByAssignee[$assigneeKey])) {
                    $ticketsByAssignee[$assigneeKey] = $tickets;
                } else {
                    $ticketsByAssignee[$assigneeKey] = array_merge($ticketsByAssignee[$assigneeKey], $tickets);
                }
            }
        }

        return $ticketsByAssignee;
    }

    /** @psalm-return array<string, array<string, JiraTicket>> */
    private function ticketsByAssigneeInStatus(string $statusName): array
    {
        $tickets = $this->jiraClient->getTickets($this->jqlUrlFactory, $statusName);
        $ticketsByAssignee = [];

        /** @var JiraTicket $ticket */
        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            if (in_array($assignee->key(), $this->jiraUsersToIgnore)) {
                continue;
            }

            if (!isset($ticketsByAssignee[$assignee->key()])) {
                $ticketsByAssignee[$assignee->key()] = [];
            }

            $ticketsByAssignee[$assignee->key()][$ticket->key()] = $ticket;
        }

        return $ticketsByAssignee;
    }
}
