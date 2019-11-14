<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Slack;

use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use Chemaclass\ScrumMaster\Jira\UrlFactoryInterface;
use Chemaclass\ScrumMaster\Slack\ReadModel\SlackTicket;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

final class SlackNotifier
{
    /** @var JiraHttpClient */
    private $jiraClient;

    /** @var SlackHttpClient */
    private $slackClient;

    /** @var Company */
    private $company;

    /** @var UrlFactoryInterface */
    private $urlFactory;

    /** @var SlackMapping */
    private $slackMapping;

    /** @var MessageGeneratorInterface */
    private $messageGenerator;

    /** @var string[] */
    private $jiraUsersToIgnore;

    public function __construct(
        JiraHttpClient $jiraClient,
        SlackHttpClient $slackClient,
        Company $company,
        UrlFactoryInterface $urlFactory,
        SlackMapping $slackMapping,
        MessageGeneratorInterface $messageGenerator,
        array $jiraUsersToIgnore = []
    ) {
        $this->jiraClient = $jiraClient;
        $this->slackClient = $slackClient;
        $this->company = $company;
        $this->urlFactory = $urlFactory;
        $this->slackMapping = $slackMapping;
        $this->messageGenerator = $messageGenerator;
        $this->jiraUsersToIgnore = $jiraUsersToIgnore;
    }

    public function sendNotifications(Board $board): SlackNotifierResult
    {
        $result = new SlackNotifierResult();

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $result->append($this->postToSlack(
                $this->getTicketsFromJiraByStatus($statusName)
            ));
        }

        return $result;
    }

    private function postToSlack(array $tickets): SlackNotifierResult
    {
        Assert::allIsInstanceOf($tickets, JiraTicket::class);
        $result = new SlackNotifierResult();

        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            if ($this->shouldIgnore($assignee)) {
                continue;
            }

            $response = $this->postTicketToSlack($ticket);
            $slackTicket = new SlackTicket($assignee->displayName(), $response->getStatusCode());
            $result->addSlackTicket($ticket->key(), $slackTicket);
        }

        return $result;
    }

    private function postTicketToSlack(JiraTicket $ticket): ResponseInterface
    {
        return $this->slackClient->postToChannel(
            $this->slackMapping->toSlackId($ticket->assignee()->name()),
            $this->messageGenerator->forJiraTicket($ticket, $this->company->companyName())
        );
    }

    private function getTicketsFromJiraByStatus(string $statusName): array
    {
        return $this->jiraClient->getTickets($this->urlFactory, $statusName);
    }

    private function shouldIgnore(Assignee $assignee): bool
    {
        return in_array($assignee->key(), $this->jiraUsersToIgnore);
    }
}
