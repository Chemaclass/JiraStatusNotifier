<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

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

    public function execute(SlackNotifierInput $input, SlackNotifierOutput $output): SlackNotifierResult
    {
        $jiraBoard = new Board($input->daysForStatus());
        $company = Company::withNameAndProject($input->companyName(), $input->jiraProjectName());

        $slackNotifier = new SlackNotifier(
            $this->jiraHttpClient,
            $this->slackHttpClient,
            $company,
            new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company)),
            SlackMapping::jiraNameWithSlackId($input->slackMappingIds()),
            SlackMessage::withTimeToDiff(new DateTimeImmutable())
        );

        $result = $slackNotifier->sendNotifications($jiraBoard);
        $output->write($result);

        return $result;
    }
}
