<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier;

use Chemaclass\JiraStatusNotifier\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\TicketsByAssigneeClient;
use Chemaclass\JiraStatusNotifier\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Jira\Board;
use Chemaclass\JiraStatusNotifier\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Jira\JqlUrlBuilder;
use Chemaclass\JiraStatusNotifier\Jira\JqlUrlFactory;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;

final class JiraConnector
{
    private JiraHttpClient $jiraHttpClient;

    /** @psalm-param list<ChannelInterface> */
    private array $channels;

    public function __construct(JiraHttpClient $jiraHttpClient, ChannelInterface...$channels)
    {
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

        $ticketsByAssignee = (new TicketsByAssigneeClient(
            $this->jiraHttpClient,
            new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company)),
            $input->jiraUsersToIgnore()
        ))->fetchFromBoard($jiraBoard);

        foreach ($this->channels as $channel) {
            $result[get_class($channel)] = $channel->send($company, $ticketsByAssignee);
        }

        return $result;
    }
}
