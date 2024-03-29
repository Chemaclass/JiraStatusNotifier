<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira;

use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\Assignee;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\JiraTicket;
use Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class JiraTicketsFactory
{
    /** @return array<int|string, string> */
    private array $customFields;

    /**
     * @param array $customFields list of custom fields to be able to use in the render templates.
     *            Usage: `['realKey' => 'newKey']` OR `['realKey']`
     */
    public function __construct(array $customFields = [])
    {
        $this->customFields = $customFields;
    }

    /**
     * @return list<JiraTicket>
     */
    public function fromJiraResponse(ResponseInterface $response): array
    {
        return $this->fromArrayIssues($response->toArray()['issues']);
    }

    /**
     * @return list<JiraTicket>
     */
    public function fromArrayIssues(array $issues): array
    {
        $jiraTickets = [];

        foreach ($issues as $item) {
            $jiraTickets[] = $this->newJiraTicket($item);
        }

        return $jiraTickets;
    }

    private function newJiraTicket(array $item): JiraTicket
    {
        $fields = $item['fields'];

        return new JiraTicket(
            $fields['summary'],
            $item['key'],
            $this->newTicketStatus($fields),
            $this->newAssignee($fields['assignee'] ?? []),
            $this->getCustomFields($fields)
        );
    }

    private function newTicketStatus(array $fields): TicketStatus
    {
        return new TicketStatus(
            $fields['status']['name'],
            new DateTimeImmutable($fields['statuscategorychangedate'])
        );
    }

    private function newAssignee(array $assignee): Assignee
    {
        if (empty($assignee)) {
            return Assignee::empty();
        }

        return new Assignee(
            $assignee['accountId'],
            $assignee['displayName']
        );
    }

    private function getCustomFields(array $fields): array
    {
        $return = [];

        foreach ($this->customFields as $key => $newKey) {
            $realKey = is_numeric($key) ? $newKey : $key;
            $return[$newKey] = $fields[$realKey] ?? null;
        }

        return $return;
    }
}
