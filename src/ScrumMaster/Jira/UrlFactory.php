<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class UrlFactory implements UrlFactoryInterface
{
    /** @var Board */
    private $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function buildJql(string $companyName, string $status, string $project): string
    {
        return JqlUrlBuilder::inOpenSprints($companyName)
            ->inProject($project)
            ->withStatus($status)
            ->statusDidNotChangeSinceDays($this->board->maxDaysInStatus($status))
            ->build();
    }
}
