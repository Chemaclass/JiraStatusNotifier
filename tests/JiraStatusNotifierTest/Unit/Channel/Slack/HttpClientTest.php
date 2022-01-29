<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Channel\Slack;

use Chemaclass\JiraStatusNotifier\Domain\Channel\Slack\HttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function post_to_channel(): void
    {
        $channelId = 'channel.id';
        $text = 'Any text';
        $asUser = true;

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects(self::once())->method('request')->with(
            $this->equalTo('POST'),
            $this->equalTo(HttpClient::SLACK_API_POST_MESSAGE),
            $this->equalTo([
                'json' => [
                    'channel' => $channelId,
                    'text' => $text,
                    'as_user' => $asUser,
                ],
            ])
        );

        $client = new HttpClient($httpClientMock);
        $client->postToChannel($channelId, $text, $asUser);
    }
}
