<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Domain\Jira\Board;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JqlUrlBuilder;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JqlUrlFactory;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\StrategyFilter\TicketFilter;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\TicketsByAssigneeClient;
use use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\TicketsByAssignee;

final class JiraConnector
{
    private JiraHttpClient $jiraHttpClient;

    private JiraConnectorInput $input;

    /** @var list<ChannelInterface> */
    private array $channels;

    /**
     * @param list<ChannelInterface> $channels
     */
    public function __construct(
        JiraHttpClient $jiraHttpClient,
        JiraConnectorInput $input,
        array $channels
    ) {
        $this->jiraHttpClient = $jiraHttpClient;
        $this->input = $input;
        $this->channels = $channels;
    }

    /**
     * It passes the tickets by assignee (from Jira) to all its channels.
     *
     * @return array<string,ChannelResult>
     */
    public function handle(): array
    {
        $jiraBoard = new Board($this->input->getDaysForStatus());
        $company = Company::withNameAndProject($this->input->getCompanyName(), $this->input->getJiraProjectName());

        $ticketsByAssignee = (new TicketsByAssigneeClient(
            $this->jiraHttpClient,
            new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company)),
            TicketFilter::notWithAssigneeKeys(...$this->input->getJiraUsersToIgnore())
        ))->fetchFromBoard($jiraBoard);

        return $this->send($company, $ticketsByAssignee);
    }

    private function send(Company $company, TicketsByAssignee $ticketsByAssignee): array
    {
        $result = [];
        foreach ($this->channels as $channel) {
            $result[get_class($channel)] = $channel->send($company, $ticketsByAssignee);
        }
        return $result;
    }
}
