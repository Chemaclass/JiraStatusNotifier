<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;

final class TicketsByAssignee
{
    /** @psalm-prop array<string, list<JiraTicket>> */
    private array $list = [];

    public function add(JiraTicket $ticket): self
    {
        $assignee = $ticket->assignee();

        if (!isset($this->list[$assignee->key()])) {
            $this->list[$assignee->key()] = [];
        }

        $this->list[$assignee->key()][] = $ticket;

        return $this;
    }

    public function list(): array
    {
        return $this->list;
    }
}
