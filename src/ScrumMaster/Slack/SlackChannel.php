<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Slack;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResultInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Chemaclass\ScrumMaster\Slack\MessageTemplate\MessageGeneratorInterface;
use function in_array;

final class SlackChannel implements ChannelInterface
{
    /** @var SlackHttpClient */
    private $slackClient;

    /** @var SlackMapping */
    private $slackMapping;

    /** @var MessageGeneratorInterface */
    private $messageGenerator;

    public function __construct(
        SlackHttpClient $slackClient,
        SlackMapping $slackMapping,
        MessageGeneratorInterface $messageGenerator
    ) {
        $this->slackClient = $slackClient;
        $this->slackMapping = $slackMapping;
        $this->messageGenerator = $messageGenerator;
    }

    public function sendNotifications(
        Board $board,
        JiraHttpClient $jiraClient,
        Company $company,
        JqlUrlFactory $jqlUrlFactory,
        array $jiraUsersToIgnore = []
    ): ChannelResultInterface {
        $result = new SlackChannelResult();

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $jiraClient->getTickets($jqlUrlFactory, $statusName);

            $result->append($this->postToSlack($company, $tickets, $jiraUsersToIgnore));
        }

        return $result;
    }

    private function postToSlack(Company $company, array $tickets, array $jiraUsersToIgnore): SlackChannelResult
    {
        $result = new SlackChannelResult();

        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            if (in_array($assignee->key(), $jiraUsersToIgnore)) {
                continue;
            }

            $response = $this->slackClient->postToChannel(
                $this->slackMapping->toSlackId($ticket->assignee()->name()),
                $this->messageGenerator->forJiraTicket($ticket, $company->companyName())
            );

            $slackTicket = ChannelIssue::withCodeAndAssignee($response->getStatusCode(), $assignee->displayName());
            $result->addChannelIssue($ticket->key(), $slackTicket);
        }

        return $result;
    }
}
