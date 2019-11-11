<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Slack;

use App\ScrumMaster\Slack\SlackHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SlackHttpClientTest extends TestCase
{
    /** @test */
    public function postToChannel(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects(self::once())->method('request')->with(
            $this->equalTo('POST'),
            $this->equalTo(SlackHttpClient::SLACK_API_POST_MESSAGE),
            $this->equalTo([
                'json' => [
                    'channel' => 'channel.id',
                    'text' => 'any text',
                    'as_user' => true,
                ],
            ])
        );

        $client = new SlackHttpClient($httpClientMock);
        $client->postToChannel('channel.id', 'any text');
    }
}
