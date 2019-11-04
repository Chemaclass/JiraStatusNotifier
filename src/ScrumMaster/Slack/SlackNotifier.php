<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\BoardInterface;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\Company;
use App\ScrumMaster\Jira\UrlFactoryInterface;

final class SlackNotifier
{
    /** @var JiraHttpClient */
    private $jiraClient;

    /** @var SlackHttpClient */
    private $slackClient;

    /** @var BoardInterface */
    private $board;

    /** @var Company */
    private $company;

    /** @var SlackMapping */
    private $slackMapping;

    /** @var MessageGeneratorInterface */
    private $messageGenerator;

    public function __construct(
        JiraHttpClient $jiraClient,
        SlackHttpClient $slackClient,
        BoardInterface $board,
        Company $company,
        SlackMapping $slackMapping,
        MessageGeneratorInterface $messageGenerator
    ) {
        $this->jiraClient = $jiraClient;
        $this->slackClient = $slackClient;
        $this->board = $board;
        $this->company = $company;
        $this->slackMapping = $slackMapping;
        $this->messageGenerator = $messageGenerator;
    }

    public function sendNotifications(UrlFactoryInterface $urlFactory): SlackNotifierResult
    {
        $result = new SlackNotifierResult();

        foreach ($this->board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($urlFactory, $statusName);
            $result->append($this->postToSlack($tickets));
        }

        return $result;
    }

    /** @return array<string,int> */
    private function postToSlack(array $tickets): SlackNotifierResult
    {
        $result = new SlackNotifierResult();

        foreach ($tickets as $ticket) {
            $response = $this->slackClient->postToChannel(
                $this->slackMapping->toSlackId($ticket->assignee()->name()),
                $this->messageGenerator->forJiraTicket($ticket, $this->company->companyName())
            );

            $result->addTicketKeyWithResponseCode($ticket->key(), $response->getStatusCode());
        }

        return $result;
    }
}
