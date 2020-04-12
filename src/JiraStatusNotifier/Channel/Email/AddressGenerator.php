<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Email;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Symfony\Component\Mime\Address;

final class AddressGenerator
{
    private ?ByPassEmail $byPassEmail;

    public function __construct(?ByPassEmail $byPassEmail = null)
    {
        $this->byPassEmail = $byPassEmail;
    }

    /** @psalm-return list<Address> */
    public function forJiraTicket(JiraTicket $ticket): array
    {
        $personName = $ticket->assignee()->displayName();

        if (!$this->byPassEmail) {
            return [new Address($ticket->assignee()->email(), $personName)];
        }

        $addresses = [];

        if ($this->byPassEmail->isSendEmailsToAssignee()) {
            $addresses[] = new Address($this->originalOrOverriddenEmail($ticket), $personName);
        }

        if (!empty($this->byPassEmail->getSendCopyTo())) {
            $addresses[] = new Address($this->byPassEmail->getSendCopyTo(), $personName);
        }

        return $addresses;
    }

    private function originalOrOverriddenEmail(JiraTicket $ticket): string
    {
        if (!$this->byPassEmail) {
            return $ticket->assignee()->key();
        }

        $assigneeKey = $ticket->assignee()->key();
        $overriddenEmail = $this->byPassEmail->getByAssigneeKey($assigneeKey);

        if ($overriddenEmail) {
            return $overriddenEmail;
        }

        return $ticket->assignee()->email();
    }
}
