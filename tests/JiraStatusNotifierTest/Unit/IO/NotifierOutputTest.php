<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\IO;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Domain\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\Domain\IO\NotifierOutputRenderer;
use Chemaclass\JiraStatusNotifier\Domain\IO\OutputInterface;
use PHPUnit\Framework\TestCase;
use Twig;

final class NotifierOutputTest extends TestCase
{
    /**
     * @test
     */
    public function render_template(): void
    {
        $templatePath = 'any_template_path';
        $channelName = 'any_channel_name';

        $result = (new ChannelResult())
            ->addChannelIssue('K-1', ChannelIssue::withStatusCode(100))
            ->addChannelIssue('K-2', ChannelIssue::withCodeAndAssignee(200, 'j.user.1'));

        $twig = $this->createMock(Twig\Environment::class);
        $twig->expects(self::once())->method('render')->with(
            $templatePath,
            [
                'channelName' => $channelName,
                'result' => $result,
                'notificationTitles' => 'K-1, K-2: j.user.1',
                'notificationSuccessful' => 'K-2',
                'notificationFailed' => 'K-1',
            ]
        );

        $output = new NotifierOutputRenderer($this->createMock(OutputInterface::class), $twig, $templatePath);
        $output->write([$channelName => $result]);
    }
}
