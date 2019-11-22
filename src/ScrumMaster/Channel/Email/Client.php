<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\Email\ReadModel\Email;
use Swift_Mailer;

final class Client
{
    const FROM = 'scrum-master@noreply.com';
    const SUBJECT = 'ScrumMaster Reminder';

    /** @var \Swift_Mailer */
    private $mailer;

    public static function withMailer(Swift_Mailer $mailer): self
    {
        return new self($mailer);
    }

    private function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMessage(Email $email): void
    {
        $message = (new \Swift_Message(self::SUBJECT))
            ->setFrom(self::FROM)
            ->setTo($email->to())
            ->setBody($email->message());

        $this->mailer->send($message);
    }
}