<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Common;

final class EnvKeys
{
    /** @var array */
    private $keys = [];

    public static function fromFile(string $content): EnvKeys
    {
        $self = new self();

        foreach (explode(PHP_EOL, $content) as $line) {
            if ($line && !static::isAComment($line)) {
                $self->keys[] = static::getKeyFromLine($line);
            }
        }

        return $self;
    }

    private static function isAComment(string $line): bool
    {
        return 0 === strpos($line, '#');
    }

    private static function getKeyFromLine(string $line): string
    {
        return substr($line, 0, strpos($line, '='));
    }

    public function validate(): void
    {
        $missingKeys = [];

        foreach ($this->keys as $key) {
            if (false === getenv($key)) {
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys) {
            throw new \Exception(implode(', ', $missingKeys) . ' keys are mandatory but missing!');
        }
    }
}
