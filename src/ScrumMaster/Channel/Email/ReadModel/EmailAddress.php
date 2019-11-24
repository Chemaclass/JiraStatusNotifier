<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email\ReadModel;

final class EmailAddress
{
    /** @var string */
    private $emailAddress;

    /** @var null|string */
    private $entityName;

    public function __construct(string $emailAddress, ?string $entityName = null)
    {
        $this->emailAddress = $emailAddress;
        $this->entityName = $entityName;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function entityName(): ?string
    {
        return $this->entityName;
    }
}
