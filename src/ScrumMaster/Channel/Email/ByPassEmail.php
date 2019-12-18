<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMaster\Channel\Email;

final class ByPassEmail
{
    /** @var string */
    private $sendCopyTo = '';

    /** @var bool */
    private $sendEmailsToAssignee = true;

    /** @var array */
    private $overriddenEmails = [];

    public function setSendCopyTo(string $email): self
    {
        $this->sendCopyTo = $email;

        return $this;
    }

    public function getSendCopyTo(): string
    {
        return $this->sendCopyTo;
    }

    /**
     * @param array $overriddenEmails Example: ['assignee.key' => 'overrided@email.com']
     */
    public function setOverriddenEmails(array $overriddenEmails): self
    {
        $this->overriddenEmails = $overriddenEmails;

        return $this;
    }

    public function setSendEmailsToAssignee(bool $bool): self
    {
        $this->sendEmailsToAssignee = $bool;

        return $this;
    }

    public function isSendEmailsToAssignee(): bool
    {
        return $this->sendEmailsToAssignee;
    }

    public function getByAssigneeKey(?string $assigneeKey): ?string
    {
        if (!$assigneeKey) {
            return null;
        }

        return $this->overriddenEmails[$assigneeKey] ?? null;
    }
}
