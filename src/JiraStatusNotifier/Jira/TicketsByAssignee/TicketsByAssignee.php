<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

final class TicketsByAssignee
{
    /** @psalm-var array<string, list<JiraTicket>> */
    private array $list = [];

    public function add(JiraTicket $ticket): self
    {
        $assignee = $ticket->assignee();

        if (!isset($this->list[$assignee->accountId()])) {
            $this->list[$assignee->accountId()] = [];
        }

        $this->list[$assignee->accountId()][] = $ticket;

        return $this;
    }

    /** @psalm-return array<string, list<JiraTicket>> */
    public function list(): array
    {
        return $this->list;
    }
}
