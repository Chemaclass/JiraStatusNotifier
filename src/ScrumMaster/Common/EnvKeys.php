<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Common;

use Chemaclass\ScrumMaster\Common\Exception\MissingKeysException;

final class EnvKeys
{
    /** @var array */
    private $envVars;

    public function __construct(array $envVars)
    {
        $this->envVars = $envVars;
    }

    public function validate(string $content): void
    {
        $lines = explode(PHP_EOL, $content);
        $missingKeys = [];

        foreach ($this->keys($lines) as $key) {
            if (!isset($this->envVars[$key])) {
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys) {
            throw new MissingKeysException($missingKeys);
        }
    }

    private function keys(array $lines): array
    {
        $keys = [];

        foreach ($lines as $line) {
            if ($line && !$this->isAComment($line)) {
                $keys[] = $this->getKeyFromLine($line);
            }
        }

        return $keys;
    }

    private function isAComment(string $line): bool
    {
        return 0 === mb_strpos($line, '#');
    }

    private function getKeyFromLine(string $line): string
    {
        return mb_substr($line, 0, mb_strpos($line, '='));
    }
}
