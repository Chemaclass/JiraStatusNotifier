<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Channel;

use Chemaclass\JiraStatusNotifier\Jira\ReadModel\JiraTicket;
use DateTimeImmutable;
use Twig;

final class MessageGenerator
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

    public function forJiraTickets(string $companyName, JiraTicket...$tickets): string
    {
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
