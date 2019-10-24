<?php

declare(strict_types=1);

namespace App\ScrumMaster;

final class JiraUrlBuilder
{
    private const BASE_URL = 'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints()';

    /** @var string */
    private $projectName;

    /** @var string */
    private $status;

    /** @var int */
    private $statusDidNotChangeSinceDays;

    public static function inProject(string $name): JiraUrlBuilder
    {
        $self = new self();
        $self->projectName = $name;

        return $self;
    }

    public function build(): string
    {
        $finalUrl = self::BASE_URL;

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
}
