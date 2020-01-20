<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Jira;

use Chemaclass\ScrumMaster\Jira\ReadModel\Company;
use function sprintf;

final class JqlUrlBuilder
{
    private const BASE_URL = 'https://%s.atlassian.net/rest/api/3/search';

    private const DEFAULT_WEEKEND_DAYS = 2;

    /** @var Company */
    private $company;

    /** @var int */
    private $weekendDays;

    /** @var string */
    private $jqlInitParam;

    /** @var null|string */
    private $status;

    /** @var null|int */
    private $statusDidNotChangeSinceDays;

    /** @var null|string */
    private $startSprintDate;

    public static function inOpenSprints(Company $company, int $weekendDays = self::DEFAULT_WEEKEND_DAYS): self
    {
        return new self($company, $weekendDays, '?jql=sprint in openSprints()');
    }

    private function __construct(Company $company, int $weekendDays, string $jqlInitParam)
    {
        $this->company = $company;
        $this->weekendDays = $weekendDays;
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

        if (null !== $this->status) {
            $finalUrl .= " AND status IN ('{$this->status}')";
        }

        if (null !== $this->statusDidNotChangeSinceDays) {
            if (null !== $this->status && null !== $this->startSprintDate) {
                $statusDidNotChangePlusWeekendDays = $this->statusDidNotChangeSinceDays + $this->weekendDays;
                $finalUrl .= ' AND (';
                $finalUrl .= "(status changed TO '{$this->status}' before '{$this->startSprintDate}' AND NOT status changed after -{$statusDidNotChangePlusWeekendDays}d)";
                $finalUrl .= ' OR ';
                $finalUrl .= "(status changed TO '{$this->status}' after '{$this->startSprintDate}' AND NOT status changed after -{$this->statusDidNotChangeSinceDays}d)";
                $finalUrl .= ')';
            } else {
                $finalUrl .= " AND NOT status changed after -{$this->statusDidNotChangeSinceDays}d";
            }
        }
//dd($finalUrl);
        return $finalUrl;
    }
}
