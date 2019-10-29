<?php

declare(strict_types=1);

namespace App\ScrumMaster\Slack;

use App\ScrumMaster\Jira\ReadModel\JiraTicket;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierResult
{
    /** @var array<string, ResponseInterface> */
    private $list = [];

    public function addTicketWithResponse(JiraTicket $ticket, ResponseInterface $response): void
    {
        $this->list[$ticket->key()] = $response;
    }

    public function list(): array
    {
        return $this->list;
    }
}
