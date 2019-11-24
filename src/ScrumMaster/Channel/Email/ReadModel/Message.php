<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email\ReadModel;

final class Message
{
    /** @var ToAddress */
    private $toAddress;

    /** @var string */
    private $body;

    public function __construct(ToAddress $toAddress, string $body)
    {
        $this->toAddress = $toAddress;
        $this->body = $body;
    }

    public function toAddresses(): array
    {
        $addresses = [];

        foreach ($this->toAddress->emailAddresses() as $address) {
            $addresses[$address->emailAddress()] = $address->entityName();
        }

        return $addresses;
    }

    public function body(): string
    {
        return $this->body;
    }
}
