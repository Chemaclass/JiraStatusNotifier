<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    private HttpClientInterface $jiraClient;

    private JiraTicketsFactory $ticketsFactory;

    public function __construct(HttpClientInterface $jiraClient, ?JiraTicketsFactory $tickets = null)
    {
        $this->jiraClient = $jiraClient;
        $this->ticketsFactory = $tickets ?? new JiraTicketsFactory();
    }

    /** @return JiraTicket[] */
    public function getTickets(UrlFactoryInterface $urlFactory, string $status): array
    {
        $response = $this->jiraClient->request('GET', $urlFactory->buildUrl($status));

        return $this->ticketsFactory->fromJiraResponse($response);
    }
}
