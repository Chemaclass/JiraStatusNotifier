<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

use App\ScrumMaster\Jira\ReadModel\Company;

interface UrlFactoryInterface
{
    public function buildUrl(Company $company, string $status): string;
}
