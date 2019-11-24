<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\Email\ReadModel\EmailAddress;
use Chemaclass\ScrumMaster\Channel\Email\ReadModel\Message;
use Chemaclass\ScrumMaster\Channel\Email\ReadModel\ToAddress;
use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;

final class Channel implements ChannelInterface
{
    /** @var MailerClient */
    private $client;

    /** @var MessageGeneratorInterface */
    private $messageGenerator;

    public function __construct(MailerClient $client, MessageGeneratorInterface $messageGenerator)
    {
        $this->messageGenerator = $messageGenerator;
        $this->client = $client;
    }

    public function sendNotifications(
        Board $board,
        JiraHttpClient $jiraClient,
        Company $company,
        JqlUrlFactory $jqlUrlFactory,
        array $jiraUsersToIgnore = []
    ): ChannelResult {
        $result = new ChannelResult();

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $jiraClient->getTickets($jqlUrlFactory, $statusName);

            $result->append($this->sendEmail($company, $tickets, $jiraUsersToIgnore));
        }

        return $result;
    }

    private function sendEmail(Company $company, array $tickets, array $jiraUsersToIgnore): ChannelResult
    {
        $result = new ChannelResult();

        /** @var JiraTicket $ticket */
        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            if (in_array($assignee->key(), $jiraUsersToIgnore)) {
                continue;
            }

            $this->client->sendMessage(new Message(
                new ToAddress([
                    new EmailAddress(
                        // $ticket->assignee()->email(), // TODO: create some mapping to "bypass" the end email of an user that
                        getenv('MAILER_USERNAME'),  // TODO: doesn't want to receive this emails specifically
                        $ticket->assignee()->displayName()
                    ),
                ]),
                $this->messageGenerator->forJiraTicket($ticket, $company->companyName())
            ));

            $slackTicket = ChannelIssue::withCodeAndAssignee(200, $assignee->displayName());
            $result->addChannelIssue($ticket->key(), $slackTicket);
        }

        return $result;
    }
}
