<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;

final class Channel implements ChannelInterface
{
    private HttpClient $slackClient;

    private JiraMapping $slackMapping;

    private MessageGeneratorInterface $messageGenerator;

    public function __construct(
        HttpClient $slackClient,
        JiraMapping $slackMapping,
        MessageGeneratorInterface $messageGenerator
    ) {
        $this->slackClient = $slackClient;
        $this->slackMapping = $slackMapping;
        $this->messageGenerator = $messageGenerator;
    }

    public function sendNotifications(array $ticketsByAssignee, Company $company): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee as $assigneeKey => $tickets) {
            $responseCode = $this->postToSlack($tickets, $company);

            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withCodeAndAssignee($responseCode, $ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }

    private function postToSlack(array $tickets, Company $company): int
    {
        $ticket = $tickets[array_key_first($tickets)];

        $response = $this->slackClient->postToChannel(
            $this->slackMapping->toSlackId($ticket->assignee()->name()),
            $this->messageGenerator->forJiraTickets($tickets, $company->companyName())
        );

        return $response->getStatusCode();
    }
}
