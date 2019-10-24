<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira\ReadModel;

final class Assignee
{
    /** @var null|string */
    private $name;

    /** @var null|string */
    private $key;

    /** @var null|string */
    private $emailAddress;

    /** @var null|string */
    private $displayName;

    public function __construct(
        ?string $name,
        ?string $key,
        ?string $emailAddress,
        ?string $displayName
    ) {
        $this->name = $name;
        $this->key = $key;
        $this->emailAddress = $emailAddress;
        $this->displayName = $displayName;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function key(): ?string
    {
        return $this->key;
    }

    public function emailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function displayName(): ?string
    {
        return $this->displayName;
    }
}
