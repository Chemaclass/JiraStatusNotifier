<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\BoardInterface;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\Company;
use App\ScrumMaster\Jira\UrlFactoryInterface;

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

    public function sendNotifications(
        Company $company,
        UrlFactoryInterface $urlFactory,
        SlackMapping $slackMapping,
        MessageGeneratorInterface $messageGenerator
    ): SlackNotifierResult {
        $result = new SlackNotifierResult();

        foreach ($this->board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($urlFactory, $statusName);

            foreach ($tickets as $ticket) {
                $response = $this->slackClient->postToChannel(
                    $slackMapping->toSlackId($ticket->assignee()->name()),
                    $messageGenerator->forJiraTicket($ticket, $company->companyName())
                );

                $result->addTicketWithResponse($ticket, $response);
            }
        }

        return $result;
    }
}
