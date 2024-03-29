<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\IO;

interface OutputInterface
{
    public function write(string $text): void;

    public function writeln(string $text): void;
}
