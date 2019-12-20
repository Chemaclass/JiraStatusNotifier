<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Common;

use Chemaclass\ScrumMaster\Common\EnvKeys;
use PHPUnit\Framework\TestCase;

final class KeysTest extends TestCase
{
    /** @test */
    public function fromEnvFile(): void
    {
        $content = <<<ENV
COMPANY_NAME=~
JIRA_USERS_TO_IGNORE='[]'
# Email Channel
MAILER_USERNAME=~
OVERRIDDEN_EMAILS='[]'

ENV;
        self::assertEquals([
            'COMPANY_NAME',
            'JIRA_USERS_TO_IGNORE',
            'MAILER_USERNAME',
            'OVERRIDDEN_EMAILS',
        ], EnvKeys::fromFile($content));
    }
}
