<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira\ReadModel;

final class Assignee
{
    /** @var string */
    private $name;

    /** @var string */
    private $key;

    /** @var string */
    private $displayName;

    /** @var string */
    private $email;

    public static function empty(): self
    {
        return new self(
            $name = '',
            $key = '',
            $displayName = '',
            $email = ''
        );
    }

    public function __construct(
        string $name,
        string $key,
        string $displayName,
        string $email
    ) {
        $this->name = $name;
        $this->key = $key;
        $this->displayName = $displayName;
        $this->email = $email;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function email(): string
    {
        return $this->email;
    }
}
