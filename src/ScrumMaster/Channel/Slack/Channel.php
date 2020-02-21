<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;

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
            $result->append($this->postToSlack($company, $tickets));
        }

        return $result;
    }

    private function postToSlack(Company $company, array $tickets): ChannelResult
    {
        $result = new ChannelResult();

        /** @var JiraTicket $ticket */
        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            $response = $this->slackClient->postToChannel(
                $this->slackMapping->toSlackId($ticket->assignee()->name()),
                $this->messageGenerator->forJiraTickets([$ticket], $company->companyName())
            );

            $issue = ChannelIssue::withCodeAndAssignee($response->getStatusCode(), $assignee->displayName());
            $result->addChannelIssue($ticket->key(), $issue);
        }

        return $result;
    }
}
