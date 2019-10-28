<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    /** @var HttpClientInterface */
    private $jiraClient;

    public function __construct(HttpClientInterface $jiraClient)
    {
        $this->jiraClient = $jiraClient;
    }

    /** @return JiraTicket[] */
    public function getTickets(UrlFactoryInterface $urlFactory, string $status): array
    {
        $response = $this->jiraClient->request('GET', $urlFactory->buildUrl($status));

        return Tickets::fromJiraResponse($response);
    }
}
