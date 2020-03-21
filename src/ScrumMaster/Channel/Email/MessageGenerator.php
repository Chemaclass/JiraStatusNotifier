<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use DateTimeImmutable;
use Twig;
use Webmozart\Assert\Assert;

final class MessageGenerator implements MessageGeneratorInterface
{
    private DateTimeImmutable $now;

    private Twig\Environment $twig;

    private string $templateName;

    public function __construct(DateTimeImmutable $now, Twig\Environment $twig, string $templateName)
    {
        $this->now = $now;
        $this->twig = $twig;
        $this->templateName = $templateName;
    }

    public function forJiraTickets(array $tickets, string $companyName): string
    {
        Assert::allIsInstanceOf($tickets, JiraTicket::class);
        uksort($tickets, 'strnatcasecmp');

        return $this->twig->render(
            $this->templateName,
            [
                'tickets' => $tickets,
                'now' => $this->now,
                'companyName' => $companyName,
            ]
        );
    }
}
