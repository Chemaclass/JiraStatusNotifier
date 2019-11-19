<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel;

interface ChannelResultInterface
{
    /** @return string[] */
    public function ticketKeys(): array;
}
