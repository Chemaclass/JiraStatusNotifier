<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira\ReadModel;

final class Company
{
    /** @var string */
    private $companyName;

    /** @var null|string */
    private $projectName;

    public static function withName(string $company): self
    {
        return new self($company);
    }

    public static function withNameAndProject(string $company, string $project): self
    {
        return new self($company, $project);
    }

    private function __construct(string $companyName, ?string $projectName = null)
    {
        $this->companyName = $companyName;
        $this->projectName = $projectName;
    }

    public function companyName(): string
    {
        return $this->companyName;
    }

    public function projectName(): ?string
    {
        return $this->projectName;
    }
}
