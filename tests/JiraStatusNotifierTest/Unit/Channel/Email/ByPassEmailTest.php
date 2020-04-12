<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Channel\Email;

use Chemaclass\JiraStatusNotifier\Channel\Email\ByPassEmail;
use Chemaclass\JiraStatusNotifier\Jira\ReadModel\Assignee;
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
        self::assertNull($byPass->getByAssigneeKey('unknown'));
        self::assertNull($byPass->getByAssigneeKey(null));
        self::assertEquals('other@email.com', $byPass->getSendCopyTo());
    }
}
