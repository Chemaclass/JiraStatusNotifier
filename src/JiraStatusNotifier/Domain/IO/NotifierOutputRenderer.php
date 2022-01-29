<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\IO;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelResult;
use Twig;

final class NotifierOutputRenderer
{
    private OutputInterface $output;

    private Twig\Environment $twig;

    private string $templateName;

    public function __construct(
        OutputInterface $output,
        Twig\Environment $twig,
        string $templateName
    ) {
        $this->output = $output;
        $this->twig = $twig;
        $this->templateName = $templateName;
    }

    /**
     * @param array<string,ChannelResult> $results
     */
    public function write(array $results): void
    {
        foreach ($results as $channelName => $result) {
            $this->writeChannel($channelName, $result);
        }
    }

    private function writeChannel(string $channelName, ChannelResult $result): void
    {
        $outputExtractor = new NotificationOutputExtractor($result);

        $render = $this->twig->render($this->templateName, [
            'channelName' => $channelName,
            'result' => $result,
            'notificationTitles' => $outputExtractor->titles(),
            'notificationSuccessful' => $outputExtractor->successful(),
            'notificationFailed' => $outputExtractor->failed(),
        ]);

        $this->output->writeln($render);
    }
}
