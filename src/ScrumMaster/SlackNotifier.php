<?php

declare(strict_types=1);

namespace App\ScrumMaster;

use App\ScrumMaster\Jira\BoardInterface;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\Company;
use App\ScrumMaster\Slack\MessageGeneratorInterface;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifier
{
    /** @var BoardInterface */
    private $board;

    /** @var JiraHttpClient */
    private $jiraClient;

    /** @var SlackHttpClient */
    private $slackClient;

    public function __construct(
        BoardInterface $board,
        JiraHttpClient $jiraClient,
        SlackHttpClient $slackClient
    ) {
        $this->board = $board;
        $this->jiraClient = $jiraClient;
        $this->slackClient = $slackClient;
    }

    /** @return ResponseInterface[] */
    public function sendNotifications(
        Company $company,
        SlackMapping $slackMapping,
        MessageGeneratorInterface $messageGenerator
    ): array {
        $responses = [];

        foreach ($this->board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($company, $statusName);

            foreach ($tickets as $ticket) {
                $responses[] = $this->slackClient->postToChannel(
                    $slackMapping->toSlackId($ticket->assignee()->name()),
                    $messageGenerator->forJiraTicket($ticket, $company->companyName())
                );
            }
        }

        return $responses;
    }
}
