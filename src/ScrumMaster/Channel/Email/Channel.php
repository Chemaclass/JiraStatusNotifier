<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\ChannelInterface;
use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;
use Chemaclass\ScrumMaster\Common\Request;
use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

final class Channel implements ChannelInterface
{
    private Mailer $mailer;

    private MessageGeneratorInterface $messageGenerator;

    private AddressGenerator $addressGenerator;

    public function __construct(
        Mailer $mailer,
        MessageGeneratorInterface $messageGenerator,
        ?AddressGenerator $addresses = null
    ) {
        $this->mailer = $mailer;
        $this->messageGenerator = $messageGenerator;
        $this->addressGenerator = $addresses ?? new AddressGenerator();
    }

    public function sendNotifications(array $ticketsByAssignee, Company $company): ChannelResult
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
            return $e->getCode();
        }
    }
}
