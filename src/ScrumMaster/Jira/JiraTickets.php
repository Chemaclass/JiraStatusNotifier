<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\Assignee;
use App\ScrumMaster\Jira\ReadModel\JiraTicket;

final class JiraTickets
{
    /** @return JiraTicket[] */
    public static function fromJira(array $rawArray): array
    {
        $jiraTickets = [];

        foreach ($rawArray['issues'] as $key => $item) {
            $fields = $item['fields'];
            $assignee = $fields['assignee'];

            $jiraTickets[] = new JiraTicket(
                $fields['summary'],
                $item['key'],
                $fields['status']['name'],
                new Assignee(
                    $assignee['name'],
                    $assignee['key'],
                    $assignee['emailAddress'],
                    $assignee['displayName']
                ),
                $storyPoints = (int)$fields['customfield_10005']
            );
        }

        return $jiraTickets;
    }
}
