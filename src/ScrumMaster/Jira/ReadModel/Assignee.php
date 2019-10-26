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
    private $displayName;

    public static function empty(): self
    {
        return new self(
            $name = null,
            $key = null,
            $displayName = null
        );
    }

    public function __construct(
        ?string $name,
        ?string $key,
        ?string $displayName
    ) {
        $this->name = $name;
        $this->key = $key;
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

    public function displayName(): ?string
    {
        return $this->displayName;
    }
}
