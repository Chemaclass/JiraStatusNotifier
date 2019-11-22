<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email\ReadModel;

final class Email
{
    /** @var string */
    private $to;

    /** @var string */
    private $message;

    public function __construct(string $to, string $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function message(): string
    {
        return $this->message;
    }
}