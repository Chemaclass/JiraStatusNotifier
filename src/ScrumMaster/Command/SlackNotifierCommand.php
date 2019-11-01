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
use App\ScrumMaster\Slack\SlackNotifierResult;
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

    public function execute(SlackNotifierInput $input, OutputInterface $output): SlackNotifierResult
    {
        $jiraBoard = new Board($input->daysForStatus());
        $company = Company::withNameAndProject($input->companyName(), $input->jiraProjectName());
        $slackNotifier = new SlackNotifier($jiraBoard, $this->jiraHttpClient, $this->slackHttpClient);

        $result = $slackNotifier->sendNotifications(
            $company,
            new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company)),
            SlackMapping::jiraNameWithSlackId($input->slackMappingIds()),
            SlackMessage::withTimeToDiff(new DateTimeImmutable())
        );

        $output->writeln(sprintf(
            'Total notifications: %d (%s)',
            count($result->list()),
            implode(', ', array_keys($result->list()))
        ));
        $output->writeln('Total successful notifications sent: ' . $this->countWithStatusCode($result, 200));
        $output->writeln('Total failed notifications sent: ' . $this->countWithStatusCode($result, 400));

        return $result;
    }

    private function countWithStatusCode(SlackNotifierResult $result, int $statusCode): int
    {
        return count(array_filter($result->list(), function (ResponseInterface $response) use ($statusCode) {
            return $response->getStatusCode() === $statusCode;
        }));
    }
}
