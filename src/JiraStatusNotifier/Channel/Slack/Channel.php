<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Slack;

use Chemaclass\JiraStatusNotifier\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\TicketsByAssignee;

final class Channel implements ChannelInterface
{
    private HttpClient $slackClient;

    private JiraMapping $slackMapping;

    private MessageGenerator $messageGenerator;

    public function __construct(
        HttpClient $slackClient,
        JiraMapping $slackMapping,
        MessageGenerator $messageGenerator
    ) {
        $this->slackClient = $slackClient;
        $this->slackMapping = $slackMapping;
        $this->messageGenerator = $messageGenerator;
    }

    public function send(Company $company, TicketsByAssignee $ticketsByAssignee): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee->list() as $assigneeKey => $tickets) {
            $responseCode = $this->postToSlack($company, ...$tickets);

            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withCodeAndAssignee($responseCode, $ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }

    private function postToSlack(Company $company, JiraTicket...$tickets): int
    {
        $ticket = $tickets[array_key_first($tickets)];

        $response = $this->slackClient->postToChannel(
            $this->slackMapping->toSlackId($ticket->assignee()->name()),
            $this->messageGenerator->forJiraTickets($company->companyName(), ...$tickets)
        );

        return $response->getStatusCode();
    }
}
