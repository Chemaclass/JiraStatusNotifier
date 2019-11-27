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
            $finalUrl .= sprintf(' AND project IN ("%s")', $this->company->projectName());
        }

        if ($this->status) {
            $finalUrl .= sprintf(' AND status IN ("%s")', $this->status);
        }

        if ($this->statusDidNotChangeSinceDays) {
            if ($this->status && $this->startSprintDate) {
                // In order to ignore the weekend between the two working weeks sprint
                $finalUrl .= sprintf(
                    ' AND ((status changed TO "%s" before %s AND NOT status changed after -%dd) OR (status changed TO "%s" after %s AND NOT status changed after -%dd))',
                    $this->status,
                    $this->startSprintDate,
                    $this->statusDidNotChangeSinceDays + 2,
                    $this->status,
                    $this->startSprintDate,
                    $this->statusDidNotChangeSinceDays
                );
            } else {
                $finalUrl .= sprintf(' AND NOT status changed after -%dd', $this->statusDidNotChangeSinceDays);
            }
        }

        return $finalUrl;
    }
}
