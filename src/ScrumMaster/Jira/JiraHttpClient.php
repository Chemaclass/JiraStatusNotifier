<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /** @return JiraTicket[] */
    public function getTickets(string $status, string $comanyName, string $projectName): array
    {
        $url = UrlFactory::factory($status, $comanyName, $projectName);
        $response = $this->client->request('GET', $url);

        return JiraTickets::fromJira($response->toArray());
    }
}
