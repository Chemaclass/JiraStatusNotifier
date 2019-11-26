<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel\Email;

use Chemaclass\ScrumMaster\Channel\Email\ByPassEmail;
use Chemaclass\ScrumMaster\Jira\ReadModel\Assignee;
use PHPUnit\Framework\TestCase;

final class ByPassEmailTest extends TestCase
{
    /** @test */
    public function overriddenEmail(): void
    {
        $assignee = new Assignee(
            $name = 'name',
            $key = 'a.key',
            $displayName = 'Display Name',
            $email = 'assignee@company.com'
        );

        $byPass = ByPassEmail::overriddenEmails([$assignee->key() => 'other2@email.com']);
        self::assertEquals('other2@email.com', $byPass->byAssigneeKey($assignee->key()));
        self::assertNull($byPass->byAssigneeKey('unknown'));
        self::assertNull($byPass->byAssigneeKey(null));
    }

    /** @test */
    public function sendAllTo(): void
    {
        $byPass = ByPassEmail::sendAllTo('other@email.com');
        self::assertEquals('other@email.com', $byPass->byAssigneeKey('unknown'));
        self::assertEquals('other@email.com', $byPass->byAssigneeKey(null));
    }
}
