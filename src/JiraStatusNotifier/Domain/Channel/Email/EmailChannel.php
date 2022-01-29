<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Channel\Email;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Domain\Common\Request;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Company;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Domain\Jira\TicketsByAssignee\TicketsByAssignee;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

final class EmailChannel implements ChannelInterface
{
    private Mailer $mailer;

    private MessageGenerator $messageGenerator;

    private AddressGenerator $addressGenerator;

    public function __construct(
        Mailer $mailer,
        MessageGenerator $messageGenerator,
        AddressGenerator $addressGenerator
    ) {
        $this->mailer = $mailer;
        $this->messageGenerator = $messageGenerator;
        $this->addressGenerator = $addressGenerator;
    }

    public function send(Company $company, TicketsByAssignee $ticketsByAssignee): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee->list() as $assigneeKey => $tickets) {
            $responseCode = $this->sendEmail($company, $tickets);

            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withCodeAndAssignee($responseCode, $ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }

    /**
     * @param list<JiraTicket> $tickets
     */
    private function sendEmail(Company $company, array $tickets): int
    {
        try {
            $ticket = $tickets[0] ?? JiraTicket::empty();
            $emailTo = $this->addressGenerator->forJiraTicket($ticket);

            if (!$emailTo) {
                return Request::HTTP_BAD_REQUEST;
            }

            $email = (new Email())
                ->to($emailTo)
                ->subject('Jira Status Notifier')
                ->addFrom('jira.status.notifier@noreply.com')
                ->html($this->messageGenerator->forJiraTickets($company->companyName(), ...$tickets));

            $this->mailer->send($email);

            return Request::HTTP_OK;
        } catch (TransportExceptionInterface $e) {
            return (int)$e->getCode();
        }
    }
}
