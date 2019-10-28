<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use App\ScrumMaster\Jira\ReadModel\TicketStatus;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class Tickets
{
    public const FIELD_STORY_POINTS = 'customfield_10005';

    /** @return JiraTicket[] */
    public static function fromJiraResponse(ResponseInterface $response): array
    {
        $jiraTickets = [];
        $rawArray = $response->toArray();

        foreach ($rawArray['issues'] ?? [] as $item) {
            $fields = $item['fields'];
            $assignee = $fields['assignee'];

            $jiraTickets[] = new JiraTicket(
                $fields['summary'],
                $item['key'],
                new TicketStatus(
                    $fields['status']['name'],
                    new DateTimeImmutable($fields['statuscategorychangedate'])
                ),
                new Assignee(
                    $assignee['name'],
                    $assignee['key'],
                    $assignee['displayName']
                ),
                $storyPoints = (int) $fields[self::FIELD_STORY_POINTS]
            );
        }

        return $jiraTickets;
    }
}
