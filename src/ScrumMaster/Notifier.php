<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\TicketsByAssignee;
use Chemaclass\ScrumMaster\IO\NotifierInput;
use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlBuilder;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;

final class Notifier
{
    private JiraHttpClient $jiraHttpClient;

    /** @var ChannelInterface[] */
    private $channels;

    public function __construct(JiraHttpClient $jiraHttpClient, array $channels)
    {
        $this->jiraHttpClient = $jiraHttpClient;
        $this->channels = $channels;
    }

    /** @return array<string,ChannelResult> */
    public function notify(NotifierInput $input): array
    {
        $jiraBoard = new Board($input->daysForStatus());
        $company = Company::withNameAndProject($input->companyName(), $input->jiraProjectName());
        $result = [];

        $ticketsByAssignee = new TicketsByAssignee(
            $this->jiraHttpClient,
            new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company)),
            $input->jiraUsersToIgnore()
        );

        foreach ($this->channels as $channel) {
            $result[get_class($channel)] = $channel->sendNotifications(
                $ticketsByAssignee->fetchFromBoard($jiraBoard),
                $company
            );
        }

        return $result;
    }
}
