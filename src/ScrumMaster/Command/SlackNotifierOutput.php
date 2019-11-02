<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command;

use App\ScrumMaster\Command\IO\OutputInterface;
use App\ScrumMaster\Slack\SlackNotifierResult;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SlackNotifierOutput
{
    /** @var OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function write(SlackNotifierResult $result): void
    {
        $this->output->writeln(sprintf(
            'Total notifications: %d (%s)',
            count($result->list()),
            implode(', ', array_keys($result->list()))
        ));

        $totalSuccessful = $totalFailed = 0;
        /** @var ResponseInterface $response */
        foreach ($result->list() as $response) {
            if ($response->getStatusCode() === 200) {
                $totalSuccessful++;
            } else {
                $totalFailed++;
            }
        }
        $this->output->writeln("Total successful notifications sent: {$totalSuccessful}");
        $this->output->writeln("Total failed notifications sent: {$totalFailed}");
    }
}
