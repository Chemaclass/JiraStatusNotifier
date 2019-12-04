<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Common\Request;
use Chemaclass\ScrumMaster\Jira\Board;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Jira\JqlUrlFactory;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class Channel implements ChannelInterface
{
    /** @var Mailer */
    private $mailer;

    /** @var MessageGeneratorInterface */
    private $messageGenerator;

    /** @var null|ByPassEmail */
    private $byPassEmail;

    public function __construct(
        Mailer $mailer,
        MessageGeneratorInterface $messageGenerator,
        ?ByPassEmail $byPassEmail = null
    ) {
        $this->messageGenerator = $messageGenerator;
        $this->mailer = $mailer;
        $this->byPassEmail = $byPassEmail;
    }

    public function sendNotifications(
        Board $board,
        JiraHttpClient $jiraClient,
        Company $company,
        JqlUrlFactory $jqlUrlFactory,
        array $jiraUsersToIgnore = []
    ): ChannelResult {
        $result = new ChannelResult();

        /**
         * @var array<string,JiraTicket[]> $ticketsByAssignee
         * For example: [$assignee->key() => [$ticket1, $ticket2]]
         */
        $ticketsByAssignee = [];

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $jiraClient->getTickets($jqlUrlFactory, $statusName);

            $ticketsByAssignee = $this->groupTicketsByAssignee($tickets, $jiraUsersToIgnore);
        }

        foreach ($ticketsByAssignee as $assigneeKey => $tickets) {
            $responseCode = $this->sendEmail($tickets, $company);
            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withCodeAndAssignee($responseCode, $ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }

    private function groupTicketsByAssignee(array $tickets, array $jiraUsersToIgnore): array
    {
        $ticketsByAssignee = [];
        /** @var JiraTicket $ticket */
        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            if (in_array($assignee->key(), $jiraUsersToIgnore)) {
                continue;
            }

            if (!isset($ticketsByAssignee[$assignee->key()])) {
                $ticketsByAssignee[$assignee->key()] = [];
            }

            $ticketsByAssignee[$assignee->key()][] = $ticket;
        }

        return $ticketsByAssignee;
    }

    private function sendEmail(array $tickets, Company $company): int
    {
        try {
            $ticket = $tickets[0];
            $email = (new Email())
                ->to(new Address($this->emailFromTicket($ticket), $ticket->assignee()->displayName()))
                ->subject('Scrum Master Reminder')
                ->addFrom('scrum.master@noreply.com')
                ->html($this->messageGenerator->forJiraTickets($tickets, $company->companyName()));

            $this->mailer->send($email);

            return Request::HTTP_OK;
        } catch (TransportExceptionInterface $e) {
            return $e->getCode();
        }
    }

    private function emailFromTicket(JiraTicket $ticket): string
    {
        if ($this->byPassEmail) {
            $assigneeKey = $ticket->assignee()->key();
            $overriddenEmail = $this->byPassEmail->byAssigneeKey($assigneeKey);

            if ($overriddenEmail) {
                return $overriddenEmail;
            }
        }

        return $ticket->assignee()->email();
    }
}
