<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

final class JqlUrlFactory implements UrlFactoryInterface
{
    /** @var Board */
    private $board;

    /** @var JqlUrlBuilder */
    private $urlBuilder;

    /** @var null|string */
    private $startSpringDate;

    public function __construct(Board $board, JqlUrlBuilder $urlBuilder, ?string $startSpringDate = null)
    {
        $this->board = $board;
        $this->urlBuilder = $urlBuilder;
        $this->startSpringDate = $startSpringDate;
    }

    public function buildUrl(string $status): string
    {
        return $this->urlBuilder
            ->withStatus($status)
            ->statusDidNotChangeSinceDays($this->board->getDaysForStatus($status), $this->startSpringDate)
            ->build();
    }
}
