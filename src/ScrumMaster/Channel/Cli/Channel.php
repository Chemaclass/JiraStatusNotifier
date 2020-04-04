<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Cli;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;

final class Channel implements ChannelInterface
{
    public function send(array $ticketsByAssignee, Company $company): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee as $assigneeKey => $tickets) {
            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withAssignee($ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }
}
