<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Slack;

use Chemaclass\ScrumMaster\Channel\MessageGeneratorInterface;
use Chemaclass\ScrumMaster\Jira\ReadModel\JiraTicket;
use DateTimeImmutable;
use Twig;
use Webmozart\Assert\Assert;

final class MessageGenerator implements MessageGeneratorInterface
{
    public const TEMPLATE_NAME = 'slack-template.twig';

    private DateTimeImmutable $now;

    private Twig\Environment $twig;

    public function __construct(DateTimeImmutable $now, Twig\Environment $twig)
    {
        $this->now = $now;
        $this->twig = $twig;
    }

    public function forJiraTickets(array $tickets, string $companyName): string
    {
        Assert::allIsInstanceOf($tickets, JiraTicket::class);
        uksort($tickets, 'strnatcasecmp');

        return $this->twig->render(
            self::TEMPLATE_NAME,
            [
                'tickets' => $tickets,
                'now' => $this->now,
                'companyName' => $companyName,
            ]
        );
    }
}
