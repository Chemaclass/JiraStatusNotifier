<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

final class JqlUrlFactory implements UrlFactoryInterface
{
    private Board $board;

    private JqlUrlBuilder $urlBuilder;

    public function __construct(Board $board, JqlUrlBuilder $urlBuilder)
    {
        $this->board = $board;
        $this->urlBuilder = $urlBuilder;
    }

    public function buildUrl(string $status): string
    {
        return $this->urlBuilder
            ->withStatus($status)
            ->statusDidNotChangeSinceDays($this->board->getDaysForStatus($status))
            ->build();
    }
}
