<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel\Email;

use Chemaclass\ScrumMaster\Channel\Email\MailerClient;
use Chemaclass\ScrumMaster\Channel\Email\ReadModel\EmailAddress;
use Chemaclass\ScrumMaster\Channel\Email\ReadModel\Message;
use Chemaclass\ScrumMaster\Channel\Email\ReadModel\ToAddress;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Message;

final class MailerClientTest extends TestCase
{
    /** @test */
    public function sendMessage(): void
    {
        $bodyMessage = 'any text';

        $swiftMailer = $this->createMock(Swift_Mailer::class);
        $swiftMailer
            ->expects(self::once())
            ->method('send')
            ->willReturnCallback(function (Swift_Message $m) use ($bodyMessage): void {
                self::assertEquals('any@mail.com', key($m->getTo()));
                self::assertEquals('Person Name', $m->getTo()['any@mail.com']);
                self::assertEquals($bodyMessage, $m->getBody());
            });

        $client = new MailerClient($swiftMailer);
        $client->sendMessage(new Message(ToAddress::withEmailAddresses([
            new EmailAddress('any@mail.com', 'Person Name'),
        ]), $bodyMessage));
    }
}
