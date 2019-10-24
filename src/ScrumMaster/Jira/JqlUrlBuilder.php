<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class JqlUrlBuilder
{
    private const BASE_URL = 'https://sevensenders.atlassian.net/rest/api/3/search';

    /** @var string */
    private $jqlInitParam;

    /** @var string */
    private $projectName;

    /** @var string */
    private $status;

    /** @var int */
    private $statusDidNotChangeSinceDays;

    public static function inOpenSprints(): JqlUrlBuilder
    {
        return new self('?jql=sprint in openSprints()');
    }

    public function __construct(string $jqlInitParam)
    {
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

    public function statusDidNotChangeSinceDays(int $days): self
    {
        $this->statusDidNotChangeSinceDays = $days;

        return $this;
    }

    public function build(): string
    {
        $finalUrl = self::BASE_URL . $this->jqlInitParam;

        if ($this->projectName) {
            $finalUrl .= sprintf(' AND project IN ("%s")', $this->projectName);
        }

        if ($this->status) {
            $finalUrl .= sprintf(' AND status IN ("%s")', $this->status);
        }

        if ($this->statusDidNotChangeSinceDays) {
            $finalUrl .= sprintf(' AND NOT status changed after -%dd', $this->statusDidNotChangeSinceDays);
        }

        return $finalUrl;
    }
}
