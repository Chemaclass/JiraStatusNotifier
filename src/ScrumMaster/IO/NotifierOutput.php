<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\IO;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Twig;

final class NotifierOutput
{
    private OutputInterface $output;

    private Twig\Environment $twig;

    public function __construct(OutputInterface $output, Twig\Environment $twig)
    {
        $this->output = $output;
        $this->twig = $twig;
    }

    /**
     * @param array<string,ChannelResult> $results
     * @param string                      $templatePath Twig template path
     */
    public function write(array $results, string $templatePath): void
    {
        foreach ($results as $channelName => $result) {
            $this->writeChannel($channelName, $result, $templatePath);
        }
    }

    private function writeChannel(string $channelName, ChannelResult $result, string $templatePath): void
    {
        $outputExtractor = new NotificationOutputExtractor($result);

        $render = $this->twig->render($templatePath, [
            'channelName' => $channelName,
            'result' => $result,
            'notificationTitles' => $outputExtractor->titles(),
            'notificationSuccessful' => $outputExtractor->successful(),
            'notificationFailed' => $outputExtractor->failed(),
        ]);

        $this->output->writeln($render);
    }
}
