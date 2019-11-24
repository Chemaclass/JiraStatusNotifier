<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\ReadModel;

use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;

final class ChannelIssue
{
    /** @var int */
    private $responseStatusCode;

    /** @var null|JiraTicket */
    private $ticket;

    public static function withStatusCode(int $responseStatusCode): self
    {
        return new self($responseStatusCode, null);
    }

    public static function withCodeAndTicket(int $responseStatusCode, JiraTicket $ticket): self
    {
        return new self($responseStatusCode, $ticket);
    }

    private function __construct(int $responseStatusCode, ?JiraTicket $ticket)
    {
        $this->responseStatusCode = $responseStatusCode;
        $this->ticket = $ticket;
    }

    public function responseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function displayName(): ?string
    {
        if (!$this->ticket) {
            return null;
        }

        return $this->ticket->assignee()->displayName();
    }

    public function ticket(): ?JiraTicket
    {
        return $this->ticket;
    }
}
