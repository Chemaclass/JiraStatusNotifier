<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\SlackNotifierCommand;
use App\ScrumMaster\Command\SlackNotifierInput;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Slack\SlackHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SlackNotifierCommandTest extends TestCase
{
    /** @test */
    public function zeroSuccessfulNotificationsWereSent(): void
    {
        $output = new InMemoryOutput();

        $command = new SlackNotifierCommand(
            new JiraHttpClient($this->createMock(HttpClientInterface::class)),
            new SlackHttpClient($this->createMock(HttpClientInterface::class))
        );

        $command->execute(SlackNotifierInput::fromArray([
            'COMPANY_NAME' => 'company',
            'JIRA_PROJECT_NAME' => 'project',
            'DAYS_FOR_STATUS' => '{"status":1}',
            'SLACK_MAPPING_IDS' => '{"jira.id":"slack.id"}',
        ]), $output);

        $this->assertEquals([
            'Total successful notifications sent: 0' . PHP_EOL,
            'Total failed notifications sent: 0' . PHP_EOL,
        ], $output->lines());
    }
}
