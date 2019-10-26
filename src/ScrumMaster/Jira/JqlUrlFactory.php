<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class JqlUrlFactory implements UrlFactoryInterface
{
    /** @var Board */
    private $board;

    /** @var JqlUrlBuilder */
    private $urlBuilder;

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
