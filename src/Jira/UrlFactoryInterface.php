<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

interface UrlFactoryInterface
{
    public function buildUrl(string $status): string;
}
