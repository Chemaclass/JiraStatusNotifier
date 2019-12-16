<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Channel\Email\ByPass;

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

        $byPass = (new ByPassEmail())->setOverriddenEmails([$assignee->key() => 'other2@email.com']);
        self::assertEquals('other2@email.com', $byPass->getByAssigneeKey($assignee->key()));
        self::assertNull($byPass->getByAssigneeKey('unknown'));
        self::assertNull($byPass->getByAssigneeKey(null));
        self::assertEmpty($byPass->getSendCopyTo());
    }

    /** @test */
    public function sendAllTo(): void
    {
        $byPass = (new ByPassEmail())->setSendCopyTo('other@email.com');
        self::assertEquals('other@email.com', $byPass->getSendCopyTo());
        self::assertEquals('other@email.com', $byPass->getByAssigneeKey('unknown'));
        self::assertEquals('other@email.com', $byPass->getByAssigneeKey(null));
    }
}
