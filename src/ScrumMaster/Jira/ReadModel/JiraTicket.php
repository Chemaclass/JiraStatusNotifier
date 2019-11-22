<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira\ReadModel;

final class JiraTicket
{
    /** @var string */
    private $title;

    /** @var string */
    private $key;

    /** @var TicketStatus */
    private $status;

    /** @var Assignee */
    private $assignee;


    /** @var null|int */
    private $storyPoints;

    public function __construct(
        string $title,
        string $key,
        TicketStatus $status,
        Assignee $assignee,
        ?int $storyPoints
    ) {
        $this->title = $title;
        $this->key = $key;
        $this->status = $status;
        $this->assignee = $assignee;
        $this->storyPoints = $storyPoints;
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

    public function storyPoints(): ?int
    {
        return $this->storyPoints;
    }
}
