<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\CompanyProject;

final class UrlFactory implements UrlFactoryInterface
{
    /** @var Board */
    private $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function buildJql(CompanyProject $company, string $status): string
    {
        return JqlUrlBuilder::inOpenSprints($company->companyName())
            ->inProject($company->project())
            ->withStatus($status)
            ->statusDidNotChangeSinceDays($this->board->maxDaysInStatus($status))
            ->build();
    }
}
