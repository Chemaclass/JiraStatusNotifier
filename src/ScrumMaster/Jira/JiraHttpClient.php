<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    /** @var HttpClientInterface */
    private $jiraClient;

    /** @var SlackHttpClient */
    private $slackClient;

    /** @var UrlFactoryInterface */
    private $urlFactory;

    /** @var CompanyProject */
    private $companyProject;

    public function __construct(
        HttpClientInterface $jiraClient,
        SlackHttpClient $slackClient,
        UrlFactoryInterface $urlFactory,
        CompanyProject $companyProject
    ) {
        $this->jiraClient = $jiraClient;
        $this->slackClient = $slackClient;
        $this->urlFactory = $urlFactory;
        $this->companyProject = $companyProject;
    }

    public function sendNotifications(SlackMapping $slackMapping, SlackMessage $slackMessage): void
    {
        foreach (Board::MAX_DAYS_IN_STATUS as $statusName => $maxDays) {
            $tickets = $this->getTickets($this->companyProject, $statusName);

            foreach ($tickets as $ticket) {
                $this->slackClient->postToChannel(
                    $slackMapping->toSlackId($ticket->assignee()->name()),
                    $slackMessage->fromJiraTicket($ticket, getenv('COMPANY_NAME'))
                );
            }
        }
    }

    /** @return JiraTicket[] */
    private function getTickets(CompanyProject $companyProject, string $status): array
    {
        $url = $this->urlFactory->buildJql($companyProject, $status);
        $response = $this->jiraClient->request('GET', $url);

        return JiraTickets::fromJira($response->toArray());
    }
}
