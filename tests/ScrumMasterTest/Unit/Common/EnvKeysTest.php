<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Common;

use Chemaclass\ScrumMaster\Common\EnvKeys;
use Chemaclass\ScrumMaster\Common\Exception\MissingKeysException;
use PHPUnit\Framework\TestCase;

final class EnvKeysTest extends TestCase
{
    /** @test */
    public function valid(): void
    {
        $content = <<<ENV
#A comment
KEY_1=~
KEY_2=null
# Another comment
KEY_3="true"
KEY_4='[]'
ENV;
        $envKeys = new EnvKeys([
            'KEY_1' => 'value1',
            'KEY_2' => 'value2',
            'KEY_3' => 'value3',
            'KEY_4' => 'value4',
        ]);
        $envKeys->validate($content);
        self::assertTrue(true);//No exception was thrown
    }

    /** @test */
    public function invalid(): void
    {
        $content = <<<ENV
#A comment
KEY_1=~
KEY_2=null
# Another comment
KEY_3="true"
KEY_4='[]'
ENV;
        self::expectExceptionObject(new MissingKeysException(['KEY_3', 'KEY_4']));
        $envKeys = new EnvKeys(['KEY_1' => 'value1', 'KEY_2' => 'value2']);
        $envKeys->validate($content);
    }
}
