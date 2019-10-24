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

            $jiraTickets[] = new JiraTicket(
                $title = $fields['summary'],
                $key = $item['key'],
                $status = $fields['status']['name'],
                new Assignee(
                    $name = $fields['assignee']['name'],
                    $key = $fields['assignee']['key'],
                    $emailAddress = $fields['assignee']['emailAddress'],
                    $displayName = $fields['assignee']['displayName']
                ),
                $storyPoints = (int)$fields['customfield_10005']
            );
        }

        return $jiraTickets;
    }
}
