<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use function sprintf;

final class JqlUrlBuilder
{
    private const BASE_URL = 'https://%s.atlassian.net/rest/api/3/search';

    /** @var Company */
    private $company;

    /** @var string */
    private $jqlInitParam;

    /** @var string */
    private $status;

    /** @var null|int */
    private $statusDidNotChangeSinceDays;

    /** @var null|string */
    private $startSprintDate;

    public static function inOpenSprints(Company $company): self
    {
        return new self($company, '?jql=sprint in openSprints()');
    }

    private function __construct(Company $company, string $jqlInitParam)
    {
        $this->company = $company;
        $this->jqlInitParam = $jqlInitParam;
    }

    public function withStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function statusDidNotChangeSinceDays(int $days, ?string $startSprintDate = null): self
    {
        $this->statusDidNotChangeSinceDays = $days;
        $this->startSprintDate = $startSprintDate;

        return $this;
    }

    public function build(): string
    {
        $finalUrl = sprintf(self::BASE_URL, $this->company->companyName()) . $this->jqlInitParam;

        if ($this->company->projectName()) {
            $finalUrl .= " AND project IN ('{$this->company->projectName()}')";
        }

        if ($this->status) {
            $finalUrl .= " AND status IN ('{$this->status}')";
        }

        if ($this->statusDidNotChangeSinceDays) {
            if ($this->status && $this->startSprintDate) {
                // In order to ignore the weekend between the two working weeks sprint
                $statusDidNotChangePlusWeekendDays = $this->statusDidNotChangeSinceDays + 2;
                $finalUrl .= " AND ((status changed TO '{$this->status}' before {$this->startSprintDate} AND NOT status changed after -{$statusDidNotChangePlusWeekendDays}d)";
                $finalUrl .= " OR (status changed TO '{$this->status}' after {$this->startSprintDate} AND NOT status changed after -{$this->statusDidNotChangeSinceDays}d))";
            } else {
                $finalUrl .= " AND NOT status changed after -{$this->statusDidNotChangeSinceDays}d";
            }
        }

        return $finalUrl;
    }
}
