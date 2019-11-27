<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email\ReadModel;

final class ToAddress
{
    /** @var EmailAddress[] */
    private $emailAddresses;

    public static function withEmailAddresses(array $emailAddresses): self
    {
        $self = new self();

        foreach ($emailAddresses as $address) {
            $self->addAddress($address);
        }

        return $self;
    }

    /** @return EmailAddress[] */
    public function emailAddresses(): array
    {
        return $this->emailAddresses;
    }

    private function addAddress(EmailAddress $address): void
    {
        $this->emailAddresses[] = $address;
    }
}
