<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

final class ByPassEmail
{
    /** @var string */
    private $sendAllTo;

    /** @var array */
    private $overriddenEmails = [];

    public static function sendAllTo(string $email): self
    {
        $self = new self();
        $self->sendAllTo = $email;

        return $self;
    }

    /**
     * @param array $overriddenEmails Example: ['assignee.key' => 'overrided@email.com']
     */
    public static function overriddenEmails(array $overriddenEmails): self
    {
        $self = new self();
        $self->overriddenEmails = $overriddenEmails;

        return $self;
    }

    public function byAssigneeKey(?string $assigneeKey): ?string
    {
        if (!empty($this->sendAllTo)) {
            return $this->sendAllTo;
        }

        if (!$assigneeKey) {
            return null;
        }

        return $this->overriddenEmails[$assigneeKey] ?? null;
    }
}
