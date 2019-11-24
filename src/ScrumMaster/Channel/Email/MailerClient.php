<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\Email\ReadModel\Message;
use Swift_Mailer;

final class MailerClient
{
    public const FROM = 'scrum-master@noreply.com';

    public const SUBJECT = 'ScrumMaster Reminder';

    /** @var Swift_Mailer */
    private $swiftMailer;

    public function __construct(Swift_Mailer $swiftMailer)
    {
        $this->swiftMailer = $swiftMailer;
    }

    public function sendMessage(Message $message): void
    {
        $message = (new \Swift_Message(self::SUBJECT))
            ->setFrom(self::FROM)
            ->setTo($message->toAddresses())
            ->setBody($message->body());

        $this->swiftMailer->send($message);
    }
}
