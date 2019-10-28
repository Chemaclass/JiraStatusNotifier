<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

use App\ScrumMaster\Command\IO\OutputInterface;
use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\JqlUrlBuilder;
use App\ScrumMaster\Jira\JqlUrlFactory;
use App\ScrumMaster\Jira\ReadModel\Company;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use App\ScrumMaster\SlackNotifier;
use DateTimeImmutable;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierCommand
{
    public function execute(SlackNotifierInput $input, OutputInterface $output): void
    {
        $jiraBoard = new Board($input->daysForStatus());
        $company = Company::withNameAndProject($input->companyName(), $input->jiraProjectName());

        $slackNotifier = new SlackNotifier(
            $jiraBoard,
            new JiraHttpClient(
                HttpClient::create([
                    'auth_basic' => [$input->jiraApiLabel(), $input->jiraApiPassword()],
                ]),
                new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company))
            ),
            new SlackHttpClient(HttpClient::create([
                'auth_bearer' => $input->slackBotUserOauthAccessToken(),
            ]))
        );

        $responses = $slackNotifier->sendNotifications(
            $company,
            SlackMapping::jiraNameWithSlackId($input->slackMappingIds()),
            SlackMessage::withTimeToDiff(new DateTimeImmutable())
        );

        $output->writeln('> Total successful notifications sent: ' . $this->countWithStatusCode($responses, 200));
        $output->writeln('> Total failed notifications sent: ' . $this->countWithStatusCode($responses, 400));
    }

    private function countWithStatusCode(array $responses, int $statusCode): int
    {
        return count(array_filter($responses, function (ResponseInterface $response) use ($statusCode) {
            return $response->getStatusCode() === $statusCode;
        }));
    }
}
