<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Command;

use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlBuilder;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Chemaclass\ScrumMaster\Slack\MessageTemplate\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Slack\SlackHttpClient;
use Chemaclass\ScrumMaster\Slack\SlackMapping;
use Chemaclass\ScrumMaster\Slack\SlackNotifier;
use Chemaclass\ScrumMaster\Slack\SlackNotifierResult;

final class SlackNotifierCommand
{
    /** @var JiraHttpClient */
    private $jiraHttpClient;

    /** @var SlackHttpClient */
    private $slackHttpClient;

    /** @var MessageGeneratorInterface */
    private $messageGenerator;

    public function __construct(
        JiraHttpClient $jiraHttpClient,
        SlackHttpClient $slackHttpClient,
        MessageGeneratorInterface $messageGenerator
    ) {
        $this->jiraHttpClient = $jiraHttpClient;
        $this->slackHttpClient = $slackHttpClient;
        $this->messageGenerator = $messageGenerator;
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
            $this->messageGenerator,
            $input->jiraUsersToIgnore()
        );

        $result = $slackNotifier->sendNotifications($jiraBoard);
        $output->write($result);

        return $result;
    }
}
