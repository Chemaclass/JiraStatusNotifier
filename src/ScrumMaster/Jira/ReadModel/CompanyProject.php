<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira\ReadModel;

final class CompanyProject
{
    /** @var string */
    private $companyName;

    /** @var string */
    private $project;

    public function __construct(string $companyName, string $project)
    {
        $this->companyName = $companyName;
        $this->project = $project;
    }

    public function companyName(): string
    {
        return $this->companyName;
    }

    public function project(): string
    {
        return $this->project;
    }
}
