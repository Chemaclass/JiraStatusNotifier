<?php

declare(strict_types=1);

namespace App\ScrumMaster;

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;

final class SlackNotifier
{
    /** @var JiraHttpClient */
    private $jiraClient;

    /** @var SlackHttpClient */
    private $slackClient;

    public function __construct(JiraHttpClient $jiraClient, SlackHttpClient $slackClient)
    {
        $this->jiraClient = $jiraClient;
        $this->slackClient = $slackClient;
    }

    public function sendNotifications(
        CompanyProject $companyProject,
        SlackMapping $slackMapping,
        SlackMessage $slackMessage
    ): void {
        foreach (Board::MAX_DAYS_IN_STATUS as $statusName => $maxDays) {
            $tickets = $this->jiraClient->getTickets($companyProject, $statusName);

            foreach ($tickets as $ticket) {
                $this->slackClient->postToChannel(
                    $slackMapping->toSlackId($ticket->assignee()->name()),
                    $slackMessage->fromJiraTicket($ticket, getenv('COMPANY_NAME'))
                );
            }
        }
    }
}
