<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackHttpClient
{
    public const SLACK_API_POST_MESSAGE = 'https://slack.com/api/chat.postMessage';

    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function postToChannel(string $channel, string $text, bool $asUser = true): ResponseInterface
    {
        return $this->client->request('POST', self::SLACK_API_POST_MESSAGE, [
            'json' => [
                'channel' => $channel,
                'text' => $text,
                'as_user' => $asUser,
            ],
        ]);
    }
}
