<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use Chemaclass\ScrumMaster\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

final class Tickets
{
    private static string $fieldStoryPoints;

    /** @return JiraTicket[] */
    public static function fromJiraResponse(ResponseInterface $response): array
    {
        return static::fromArrayIssues($response->toArray()['issues']);
    }

    /** @return JiraTicket[] */
    public static function fromArrayIssues(array $issues): array
    {
        $jiraTickets = [];

        foreach ($issues ?? [] as $item) {
            $jiraTickets[] = static::newJiraTicket($item);
        }

        return $jiraTickets;
    }

    private static function newJiraTicket(array $item): JiraTicket
    {
        $fields = $item['fields'];

        return new JiraTicket(
            $fields['summary'],
            $item['key'],
            static::newTicketStatus($fields),
            static::newAssignee($fields['assignee'] ?? []),
            $storyPoints = (int) $fields[self::$fieldStoryPoints]
        );
    }

    private static function newTicketStatus(array $fields): TicketStatus
    {
        return new TicketStatus(
            $fields['status']['name'],
            new DateTimeImmutable($fields['statuscategorychangedate'])
        );
    }

    private static function newAssignee(array $assignee): Assignee
    {
        if (empty($assignee)) {
            return Assignee::empty();
        }

        return new Assignee(
            $assignee['name'] ?? '',
            $assignee['key'] ?? '',
            $assignee['displayName'] ?? '',
            $assignee['emailAddress'] ?? ''
        );
    }

    public function __construct(string $fieldStoryPoints)
    {
        Assert::notEmpty($fieldStoryPoints);
        self::$fieldStoryPoints = $fieldStoryPoints;
    }

    public function getFieldStoryPoints(): string
    {
        return self::$fieldStoryPoints;
    }
}
