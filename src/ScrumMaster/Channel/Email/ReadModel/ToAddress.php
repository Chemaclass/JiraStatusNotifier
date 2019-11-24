<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email\ReadModel;

use Webmozart\Assert\Assert;

final class ToAddress
{
    /** @var EmailAddress[] */
    private $emailAddresses;

    public function __construct(array $emailAddresses)
    {
        Assert::allIsInstanceOf($emailAddresses, EmailAddress::class);
        $this->emailAddresses = $emailAddresses;
    }

    /** @return EmailAddress[] */
    public function emailAddresses(): array
    {
        return $this->emailAddresses;
    }
}
