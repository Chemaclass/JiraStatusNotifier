<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier\Domain\Common;

use Chemaclass\JiraStatusNotifier\Domain\Common\Exception\MissingKeysException;

/** @psalm-immutable */
final class EnvKeys
{
    /** @psalm-var list<string> */
    private $envVars;

    public static function create(array $envVars): self
    {
        return new self($envVars);
    }

    private function __construct(array $envVars)
    {
        $this->envVars = $envVars;
    }

    /**
     * @param string $content The ".env.dist" content
     *
     * @throws MissingKeysException
     */
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
        $pos = mb_strpos($line, '=');

        if (false === $pos) {
            return '';
        }

        return mb_substr($line, 0, $pos);
    }
}
