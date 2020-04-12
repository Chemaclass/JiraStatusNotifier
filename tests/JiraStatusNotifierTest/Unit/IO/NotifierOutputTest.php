<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\IO;

use Chemaclass\JiraStatusNotifier\Channel\ChannelResult;
use Chemaclass\JiraStatusNotifier\Channel\ReadModel\ChannelIssue;
use Chemaclass\JiraStatusNotifier\IO\NotifierOutput;
use Chemaclass\JiraStatusNotifier\IO\OutputInterface;
use PHPUnit\Framework\TestCase;
use Twig;

final class NotifierOutputTest extends TestCase
{
    /** @test */
    public function renderTemplate(): void
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

        $output = new NotifierOutput($this->createMock(OutputInterface::class), $twig);
        $output->write([$channelName => $result], $templatePath);
    }
}
