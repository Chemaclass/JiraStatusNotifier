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
            ->statusDidNotChangeSinceDays(Board::SLA[$status])
            ->build();
    }

    public static function inReview(string $comanyName, string $project): string
    {
        return JqlUrlBuilder::inOpenSprints($comanyName)
            ->inProject($project)
            ->withStatus("In Review")
            ->statusDidNotChangeSinceDays(1)
            ->build();
    }

    public static function inQA(string $comanyName, string $project)
    {
        return JqlUrlBuilder::inOpenSprints($comanyName)
            ->inProject($project)
            ->withStatus("In QA")
            ->statusDidNotChangeSinceDays(2)
            ->build();
    }
}
