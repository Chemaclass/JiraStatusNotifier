<?php

declare(strict_types=1);

namespace App\ScrumMaster\ReadModel;

final class JiraTicket
{
    /** @var string */
    private $title;

    /** @var string */
    private $key;

    /** @var Assignee */
    private $assignee;

    /** @var int|null */
    private $storyPoints;

    public function __construct(string $title, string $key, Assignee $assignee, ?int $storyPoints)
    {
        $this->title = $title;
        $this->key = $key;
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

    public function assignee(): Assignee
    {
        return $this->assignee;
    }

    public function storyPoints(): ?int
    {
        return $this->storyPoints;
    }
}
