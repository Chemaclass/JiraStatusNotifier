<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\Slack\HttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientTest extends TestCase
{
    /** @test */
    public function postToChannel(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects(self::once())->method('request')->with(
            $this->equalTo('POST'),
            $this->equalTo(HttpClient::SLACK_API_POST_MESSAGE),
            $this->equalTo([
                'json' => [
                    'channel' => 'channel.id',
                    'text' => 'any text',
                    'as_user' => true,
                ],
            ])
        );

        $client = new HttpClient($httpClientMock);
        $client->postToChannel('channel.id', 'any text');
    }
}
