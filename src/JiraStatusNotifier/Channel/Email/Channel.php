<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Email;

use Chemaclass\JiraStatusNotifier\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Common\Request;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Company;
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

    public function send(array $ticketsByAssignee, Company $company): ChannelResult
    {
        $result = new ChannelResult();

        foreach ($ticketsByAssignee as $assigneeKey => $tickets) {
            $responseCode = $this->sendEmail($tickets, $company);

            foreach ($tickets as $ticket) {
                $issue = ChannelIssue::withCodeAndAssignee($responseCode, $ticket->assignee()->displayName());
                $result->addChannelIssue($ticket->key(), $issue);
            }
        }

        return $result;
    }

    private function sendEmail(array $tickets, Company $company): int
    {
        try {
            $ticket = $tickets[array_key_first($tickets)];

            $email = (new Email())
                ->to(...$this->addressGenerator->forJiraTicket($ticket))
                ->subject('Scrum Master Reminder')
                ->addFrom('scrum.master@noreply.com')
                ->html($this->messageGenerator->forJiraTickets($tickets, $company->companyName()));

            $this->mailer->send($email);

            return Request::HTTP_OK;
        } catch (TransportExceptionInterface $e) {
            return (int) $e->getCode();
        }
    }
}
