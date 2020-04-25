<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Email;

use Chemaclass\JiraStatusNotifier\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Common\Request;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Jira\TicketsByAssignee\TicketsByAssignee;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

final class Channel implements ChannelInterface
{
    private Mailer $mailer;

    private MessageGenerator $messageGenerator;

    private AddressGenerator $addressGenerator;

    public function __construct(
        Mailer $mailer,
        MessageGenerator $messageGenerator,
        ?AddressGenerator $addresses = null
    ) {
        $this->mailer = $mailer;
        $this->messageGenerator = $messageGenerator;
        $this->addressGenerator = $addresses ?? new AddressGenerator();
    }

    public function send(Company $company, TicketsByAssignee $ticketsByAssignee): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee->list() as $assigneeKey => $tickets) {
            $responseCode = $this->sendEmail($company, ...$tickets);

            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withCodeAndAssignee($responseCode, $ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }

    private function sendEmail(Company $company, JiraTicket...$tickets): int
    {
        try {
            $ticket = $tickets[array_key_first($tickets)];

            $email = (new Email())
                ->to(...$this->addressGenerator->forJiraTicket($ticket))
                ->subject('Scrum Master Reminder')
                ->addFrom('scrum.master@noreply.com')
                ->html($this->messageGenerator->forJiraTickets($company->companyName(), ...$tickets));

            $this->mailer->send($email);

            return Request::HTTP_OK;
        } catch (TransportExceptionInterface $e) {
            return (int)$e->getCode();
        }
    }
}
