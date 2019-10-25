<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\CompanyProject;

interface UrlFactoryInterface
{
    public function buildUrl(CompanyProject $company, string $status): string;
}
