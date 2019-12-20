<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Common;

final class Keys
{
    public static function fromEnvFile(string $content): array
    {
        $keys = [];

        foreach (explode(PHP_EOL, $content) as $line) {
            if ($line && !static::isAComment($line)) {
                $keys[] = static::getKeyFromLine($line);
            }
        }

        return $keys;
    }

    private static function isAComment(string $line): bool
    {
        return 0 === strpos($line, '#');
    }

    private static function getKeyFromLine(string $line): string
    {
        return substr($line, 0, strpos($line, '='));
    }
}
