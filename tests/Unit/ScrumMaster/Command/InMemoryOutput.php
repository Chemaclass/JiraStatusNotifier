<?php

declare(strict_types=1);

namespace App\Tests\Unit\ScrumMaster\Command;

use App\ScrumMaster\Command\IO\OutputInterface;

final class InMemoryOutput implements OutputInterface
{
    private $lines = [];

    public function write(string $text): void
    {
        $this->lines[] = $text;
    }

    public function writeln(string $text): void
    {
        $this->lines[] = $text . PHP_EOL;
    }

    public function lines(): array
    {
        return $this->lines;
    }
}
