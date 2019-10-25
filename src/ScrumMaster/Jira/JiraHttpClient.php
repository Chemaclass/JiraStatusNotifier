<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    /** @var HttpClientInterface */
    private $client;

    /** @var UrlFactoryInterface */
    private $urlFactory;

    public function __construct(HttpClientInterface $client, UrlFactoryInterface $urlFactory)
    {
        $this->client = $client;
        $this->urlFactory = $urlFactory;
    }

    /** @return JiraTicket[] */
    public function getTickets(string $comanyName, string $status, string $projectName): array
    {
        $url = $this->urlFactory->buildJql($comanyName, $status, $projectName);
        $response = $this->client->request('GET', $url);

        return JiraTickets::fromJira($response->toArray());
    }
}
