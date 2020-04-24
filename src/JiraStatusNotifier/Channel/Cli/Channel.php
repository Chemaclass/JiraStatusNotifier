<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Cli;

use Chemaclass\JiraStatusNotifier\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Channel\TicketsByAssignee;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;

final class Channel implements ChannelInterface
{
    public function send(Company $company, TicketsByAssignee $ticketsByAssignee): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee->list() as $assigneeKey => $tickets) {
            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withAssignee($ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }
}
