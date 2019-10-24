<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackHttpClient
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function postToChannel(string $channel, string $text): ResponseInterface
    {
        return $this->client->request('POST', 'https://slack.com/api/chat.postMessage', [
            'json' => [
                'as_user' => true,
                'channel' => $channel,
                'text' => $text,
            ],
        ]);
    }
}
