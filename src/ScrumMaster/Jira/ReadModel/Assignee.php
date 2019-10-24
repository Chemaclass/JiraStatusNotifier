<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira\ReadModel;

final class Assignee
{
    /** @var string */
    private $name;

    /** @var string */
    private $key;

    /** @var string */
    private $emailAddress;

    /** @var string */
    private $displayName;

    public function __construct(
        string $name,
        string $key,
        string $emailAddress,
        string $displayName
    ) {
        $this->name = $name;
        $this->key = $key;
        $this->emailAddress = $emailAddress;
        $this->displayName = $displayName;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }
}
