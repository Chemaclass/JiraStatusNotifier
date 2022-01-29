<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Channel\Cli;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\TicketsByAssignee;

final class CliChannel implements ChannelInterface
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
