<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

function checkRequirements(): void
{
    if (PHP_VERSION_ID < 70200) {
        throw new \RuntimeException('You need PHP 7.2 to run this application');
    }
}

checkRequirements();
