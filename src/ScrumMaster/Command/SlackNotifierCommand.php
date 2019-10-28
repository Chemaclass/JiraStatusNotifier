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
use App\ScrumMaster\Slack\SlackNotifier;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierCommand
{
    /** @var JiraHttpClient */
    private $jiraHttpClient;

    /** @var SlackHttpClient */
    private $slackHttpClient;

    public function __construct(JiraHttpClient $jiraHttpClient, SlackHttpClient $slackHttpClient)
    {
        $this->jiraHttpClient = $jiraHttpClient;
        $this->slackHttpClient = $slackHttpClient;
    }

    public function execute(SlackNotifierInput $input, OutputInterface $output): void
    {
        $jiraBoard = new Board($input->daysForStatus());
        $company = Company::withNameAndProject($input->companyName(), $input->jiraProjectName());
        $slackNotifier = new SlackNotifier($jiraBoard, $this->jiraHttpClient, $this->slackHttpClient);

        $responses = $slackNotifier->sendNotifications(
            $company,
            new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company)),
            SlackMapping::jiraNameWithSlackId($input->slackMappingIds()),
            SlackMessage::withTimeToDiff(new DateTimeImmutable())
        );

        $output->writeln('Total notifications: ' . count($responses));
        $output->writeln('Total successful notifications sent: ' . $this->countWithStatusCode($responses, 200));
        $output->writeln('Total failed notifications sent: ' . $this->countWithStatusCode($responses, 400));
    }

    private function countWithStatusCode(array $responses, int $statusCode): int
    {
        return count(array_filter($responses, function (ResponseInterface $response) use ($statusCode) {
            return $response->getStatusCode() === $statusCode;
        }));
    }
}
