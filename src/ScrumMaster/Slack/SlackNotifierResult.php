<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;

final class SlackNotifierResult
{
    /** @var array<string,int> */
    private $list = [];

    public function addTicketWithResponseCode(JiraTicket $ticket, int $response): void
    {
        $this->list[$ticket->key()] = $response;
    }

    /** @return array<string,int> */
    public function list(): array
    {
        return $this->list;
    }
}
