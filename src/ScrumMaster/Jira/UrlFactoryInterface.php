<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

interface UrlFactoryInterface
{
    public function buildJql(string $companyName, string $status, string $project): string;
}
