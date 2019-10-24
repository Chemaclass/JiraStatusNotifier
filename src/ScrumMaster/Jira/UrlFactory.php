<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class UrlFactory
{
    public static function inReview(string $comanyName, string $project): string
    {
        return JqlUrlBuilder::inOpenSprints($comanyName)
            ->inProject($project)
            ->withStatus("In Review")
            ->statusDidNotChangeSinceDays(1)
            ->build();
    }
}
