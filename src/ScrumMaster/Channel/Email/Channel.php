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

        foreach ($board->maxDaysInStatus() as $statusName => $maxDays) {
            $tickets = $jiraClient->getTickets($jqlUrlFactory, $statusName);

            $result->append($this->sendEmails($company, $tickets, $jiraUsersToIgnore));
        }

        return $result;
    }

    private function sendEmails(Company $company, array $tickets, array $jiraUsersToIgnore): ChannelResult
    {
        $result = new ChannelResult();

        /** @var JiraTicket $ticket */
        foreach ($tickets as $ticket) {
            $assignee = $ticket->assignee();

            if (in_array($assignee->key(), $jiraUsersToIgnore)) {
                continue;
            }

            $responseCode = $this->sendEmail($ticket, $company);
            $issue = ChannelIssue::withCodeAndAssignee($responseCode, $assignee->displayName());
            $result->addChannelIssue($ticket->key(), $issue);
        }

        return $result;
    }

    private function sendEmail(JiraTicket $ticket, Company $company): int
    {
        try {
            $email = (new Email())
                ->to(new Address($this->emailFromTicket($ticket), $ticket->assignee()->displayName()))
                ->subject('Scrum Master Reminder')
                ->addFrom('scrum.master@noreply.com')
                ->html($this->messageGenerator->forJiraTicket($ticket, $company->companyName()));

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
