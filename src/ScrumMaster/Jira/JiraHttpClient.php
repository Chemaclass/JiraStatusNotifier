<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JiraHttpClient
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function inReview(string $comanyName, string $projectName): array
    {
        $url = UrlFactory::inReview($comanyName, $projectName);
        $response = $this->client->request('GET', $url);

        return JiraTickets::fromJira($response->toArray());
    }

    public function inQA(string $comanyName, string $projectName)
    {
        $url = UrlFactory::inQA($comanyName, $projectName);
        $response = $this->client->request('GET', $url);

        return JiraTickets::fromJira($response->toArray());
    }
}
