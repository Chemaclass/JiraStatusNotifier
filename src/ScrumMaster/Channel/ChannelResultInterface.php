<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel;

use Chemaclass\ScrumMaster\Channel\ReadModel\ChannelIssue;

interface ChannelResultInterface
{
    /** @return array<string, ChannelIssue> */
    public function channelIssues(): array;

    /** @return string[] */
    public function channelIssuesKeys(): array;
}
