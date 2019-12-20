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
#A comment
KEY_1=~
KEY_2=null
# Another comment
KEY_3="true"
KEY_4='[]'

ENV;
        self::assertEquals([
            'KEY_1',
            'KEY_2',
            'KEY_3',
            'KEY_4',
        ], EnvKeys::fromFile($content)->keys());
    }
}
