<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class JqlUrlBuilder
{
    private const BASE_URL = 'https://%s.atlassian.net/rest/api/3/search';

    /** @var string */
    private $companyName;

    /** @var string */
    private $jqlInitParam;

    /** @var string */
    private $projectName;

    /** @var string */
    private $status;

    /** @var int */
    private $statusDidNotChangeSinceDays;

    /** @var string|null */
    private $startSprintDate;

    public static function inOpenSprints(string $companyName): JqlUrlBuilder
    {
        return new self($companyName, '?jql=sprint in openSprints()');
    }

    private function __construct(string $companyName, string $jqlInitParam)
    {
        $this->companyName = $companyName;
        $this->jqlInitParam = $jqlInitParam;
    }

    public function inProject(string $name): self
    {
        $this->projectName = $name;

        return $this;
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
        $finalUrl = sprintf(self::BASE_URL, $this->companyName) . $this->jqlInitParam;

        if ($this->projectName) {
            $finalUrl .= sprintf(' AND project IN ("%s")', $this->projectName);
        }

        if ($this->status) {
            $finalUrl .= sprintf(' AND status IN ("%s")', $this->status);
        }

        if ($this->statusDidNotChangeSinceDays) {
            if ($this->status && $this->startSprintDate) {
                $finalUrl .= sprintf(' AND ((status changed TO %s before %s AND NOT status changed after -%dd) OR (status changed TO %s after %s AND NOT status changed after -%dd))',
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
