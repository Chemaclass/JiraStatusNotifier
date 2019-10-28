<?php

declare(strict_types=1);

namespace App\ScrumMaster\Command\IO;

interface OutputInterface
{
    public function write(string $text): void;

    public function writeln(string $text): void;
}
