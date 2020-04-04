<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    private HttpClientInterface $jiraClient;

    private JiraTicketsFactory $tickets;

    public function __construct(HttpClientInterface $jiraClient, JiraTicketsFactory $tickets)
    {
        $this->jiraClient = $jiraClient;
        $this->tickets = $tickets;
    }

    /** @return JiraTicket[] */
    public function getTickets(UrlFactoryInterface $urlFactory, string $status): array
    {
        $response = $this->jiraClient->request('GET', $urlFactory->buildUrl($status));

        return $this->tickets->fromJiraResponse($response);
    }
}
