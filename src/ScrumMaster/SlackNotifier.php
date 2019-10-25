<?php

declare(strict_types=1);

namespace App\ScrumMaster;

use App\ScrumMaster\Jira\BoardInterface;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifier
{
    /** @var BoardInterface */
    private $board;

    /** @var JiraHttpClient */
    private $jiraClient;

    /** @var SlackHttpClient */
    private $slackClient;

    public function __construct(BoardInterface $board, JiraHttpClient $jiraClient, SlackHttpClient $slackClient)
    {
        $this->board = $board;
        $this->jiraClient = $jiraClient;
        $this->slackClient = $slackClient;
    }

    /** @return ResponseInterface[] */
    public function sendNotifications(
        CompanyProject $company,
        SlackMapping $slackMapping,
        SlackMessage $slackMessage
    ): array {
        $responses = [];

        foreach ($this->board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($company, $statusName);

            foreach ($tickets as $ticket) {
                $responses = $this->slackClient->postToChannel(
                    $slackMapping->toSlackId($ticket->assignee()->name()),
                    $slackMessage->fromJiraTicket($ticket, $company->companyName())
                );
            }
        }

        return $responses;
    }
}
