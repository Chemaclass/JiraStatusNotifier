<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\Company;

final class JqlUrlFactory implements UrlFactoryInterface
{
    /** @var Board */
    private $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function buildUrl(Company $company, string $status): string
    {
        return JqlUrlBuilder::inOpenSprints($company)
            ->withStatus($status)
            ->statusDidNotChangeSinceDays($this->board->getDaysForStatus($status))
            ->build();
    }
}
