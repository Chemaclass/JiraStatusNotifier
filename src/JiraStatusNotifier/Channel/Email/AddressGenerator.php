<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel\Email;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use Symfony\Component\Mime\Address;

final class AddressGenerator
{
    private array $jiraIdsToEmail;

    public function __construct(array $jiraIdsToEmail)
    {
        $this->jiraIdsToEmail = $jiraIdsToEmail;
    }

    public function forJiraTicket(JiraTicket $ticket): ?Address
    {
        $assignee = $ticket->assignee();
        $email = $this->jiraIdsToEmail[$assignee->accountId()] ?? '';

        if (!$email) {
            return null;
        }

        return new Address($email, $assignee->displayName());
    }

}
