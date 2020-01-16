<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\IO;

use Chemaclass\ScrumMaster\Channel\ChannelResult;
use Twig\Environment;

final class NotifierOutput
{
    /** @var OutputInterface */
    private $output;

    /** @var Environment */
    private $twig;

    public function __construct(OutputInterface $output, Environment $twig)
    {
        $this->output = $output;
        $this->twig = $twig;
    }

    /** @param array<string,ChannelResult> $results */
    public function write(array $results): void
    {
        foreach ($results as $channelName => $result) {
            $this->writeChannel($channelName, $result);
        }
    }

    private function writeChannel(string $channelName, ChannelResult $result): void
    {
        $outputExtractor = new NotificationOutputExtractor($result);

        $render = $this->twig->render('output/channel-result.twig', [
            'channelName' => $channelName,
            'result' => $result,
            'notificationTitles' => $outputExtractor->titles(),
            'notificationSuccessful' => $outputExtractor->successful(),
            'notificationFailed' => $outputExtractor->failed(),
        ]);

        $this->output->writeln($render);
    }
}
