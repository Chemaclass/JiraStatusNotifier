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

        $this->output->writeln('Total successful notifications sent: ' . $this->countWithStatusCode($result, 200));
        $this->output->writeln('Total failed notifications sent: ' . $this->countWithStatusCode($result, 400));
    }

    private function countWithStatusCode(SlackNotifierResult $result, int $statusCode): int
    {
        return count(array_filter($result->list(), function (ResponseInterface $response) use ($statusCode) {
            return $response->getStatusCode() === $statusCode;
        }));
    }
}
