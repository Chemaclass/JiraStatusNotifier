<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\TicketsByAssignee;
use Chemaclass\ScrumMaster\IO\JiraConnectorInput;
use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlBuilder;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Webmozart\Assert\Assert;

final class JiraConnector
{
    private JiraHttpClient $jiraHttpClient;

    /** @var ChannelInterface[] */
    private $channels;

    public function __construct(JiraHttpClient $jiraHttpClient, array $channels)
    {
        Assert::allIsInstanceOf($channels, ChannelInterface::class);
        $this->jiraHttpClient = $jiraHttpClient;
        $this->channels = $channels;
    }

    /**
     * It passes the tickets by assignee (from Jira) to all its channels.
     *
     * @return array<string,ChannelResult>
     */
    public function handle(JiraConnectorInput $input): array
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
            $result[get_class($channel)] = $channel->send(
                $ticketsByAssignee->fetchFromBoard($jiraBoard),
                $company
            );
        }

        return $result;
    }
}
