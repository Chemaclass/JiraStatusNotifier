<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\IO;

final class EchoOutput implements OutputInterface
{
    public function write(string $text): void
    {
        print $text;
    }

    public function writeln(string $text): void
    {
        print $text . PHP_EOL;
    }
}
