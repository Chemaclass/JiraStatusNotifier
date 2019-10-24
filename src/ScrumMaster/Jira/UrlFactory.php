<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class UrlFactory
{
    public static function factory(string $status, string $comanyName, string $project): string
    {
        return JqlUrlBuilder::inOpenSprints($comanyName)
            ->inProject($project)
            ->withStatus($status)
            ->statusDidNotChangeSinceDays(Board::maxDaysInStatus($status))
            ->build();
    }
}
