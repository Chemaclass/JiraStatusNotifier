<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Jira\ReadModel;

final class JiraTicket
{
    private string $title;

    private string $key;

    private TicketStatus $status;

    private Assignee $assignee;

    private array $customFields;

    public function __construct(
        string $title,
        string $key,
        TicketStatus $status,
        Assignee $assignee,
        array $customFields = []
    ) {
        $this->title = $title;
        $this->key = $key;
        $this->status = $status;
        $this->assignee = $assignee;
        $this->customFields = $customFields;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function status(): TicketStatus
    {
        return $this->status;
    }

    public function assignee(): Assignee
    {
        return $this->assignee;
    }

    public function customFields(): array
    {
        return $this->customFields;
    }
}
