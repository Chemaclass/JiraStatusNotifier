<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Command;

use Chemaclass\ScrumMaster\Command\IO\OutputInterface;

final class InMemoryOutput implements OutputInterface
{
    private $lines = [];

    public function write(string $text): void
    {
        $this->lines[] = $text;
    }

    public function writeln(string $text): void
    {
        $this->lines[] = $text;
    }

    public function lines(): array
    {
        return $this->lines;
    }
}
