<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Gacela\Framework\AbstractFacade;

/**
 * @method JiraStatusNotifierFactory getFactory()
 */
final class JiraStatusNotifierFacade extends AbstractFacade
{
    /**
     * It passes the tickets by assignee (from Jira) to all its channels.
     *
     * @param list<class-string> $channels
     *
     * @return array<string,ChannelResult>
     */
    public function handle(array $channels): array
    {
        return $this->getFactory()
            ->createJiraConnector($channels)
            ->handle();
    }

    /**
     * @param array<string,ChannelResult> $result
     */
    public function renderOutput(array $result): void
    {
        $this->getFactory()
            ->createNotifierOutputRenderer()
            ->write($result);
    }
}
