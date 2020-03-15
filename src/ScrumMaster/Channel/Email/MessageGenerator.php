<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use DateTimeImmutable;
use Twig;

final class MessageGenerator implements MessageGeneratorInterface
{
    private DateTimeImmutable $now;

    private Twig\Environment $twig;

    public function __construct(DateTimeImmutable $now, Twig\Environment $twig)
    {
        $this->now = $now;
        $this->twig = $twig;
    }

    public function forJiraTickets(array $tickets, string $companyName): string
    {
        uksort($tickets, 'strnatcasecmp');

        return $this->twig->render('email-template.twig', [
            'tickets' => $tickets,
            'now' => $this->now,
            'companyName' => $companyName,
        ]);
    }
}
