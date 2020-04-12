<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Jira;

interface UrlFactoryInterface
{
    public function buildUrl(string $status): string;
}
