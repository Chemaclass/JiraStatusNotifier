<?php

declare(strict_types=1);

namespace App\ScrumMaster\Jira;

final class Board implements BoardInterface
{
    public const TODO = 'To Do';

    public const BLOCKED = 'Blocked';

    public const IN_PROGRESS = 'In Progress';

    public const IN_REVIEW = 'In Review';

    public const READY_FOR_QA = 'Ready for QA';

    public const IN_QA = 'IN QA';

    public const VERIFIED = 'Verified';

    public const READY_FOR_RC = 'Ready For RC';

    public const IN_RC = 'IN RC';

    public const SLA_TO_DO = 10;

    public const SLA_BLOCKED = 7;

    public const SLA_IN_PROGRESS = 4;

    public const SLA_IN_REVIEW = 1;

    public const SLA_READY_FOR_QA = 1;

    public const SLA_IN_QA = 2;

    public const SLA_VERIFIED = 3;

    public const SLA_READY_FOR_RC = 1;

    public const SLA_IN_RC = 4;

    public const MAX_DAYS_FALLBACK = 1;

    public const MAX_DAYS_IN_STATUS = [
        self::TODO => self::SLA_TO_DO,
        self::BLOCKED => self::SLA_BLOCKED,
        self::IN_PROGRESS => self::SLA_IN_PROGRESS,
        self::IN_REVIEW => self::SLA_IN_REVIEW,
        self::READY_FOR_QA => self::SLA_READY_FOR_QA,
        self::IN_QA => self::SLA_IN_QA,
        self::VERIFIED => self::SLA_VERIFIED,
        self::READY_FOR_RC => self::SLA_READY_FOR_RC,
        self::IN_RC => self::SLA_IN_RC,
    ];

    public function maxDaysInStatus(string $status): int
    {
        return self::MAX_DAYS_IN_STATUS[$status] ?? self::MAX_DAYS_FALLBACK;
    }
}
