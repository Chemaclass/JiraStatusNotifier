<?php

declare(strict_types=1);

namespace Chemaclass\ScrumMasterTests\Unit\Concerns;

use Chemaclass\ScrumMaster\Jira\Tickets;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @method createMock($originalClassName): MockObject
 */
trait JiraApiResource
{
    private function mockJiraClient(array $issues): HttpClientInterface
    {
        $jiraResponse = $this->createMock(ResponseInterface::class);
        $jiraResponse->method('toArray')->willReturn(['issues' => $issues]);

        /** @var HttpClientInterface|MockObject $jiraClient */
        $jiraClient = $this->createMock(HttpClientInterface::class);
        $jiraClient->method('request')->willReturn($jiraResponse);

        return $jiraClient;
    }

    private function createAJiraIssueAsArray(string $assigneeKey, string $key, string $email = 'user@email.jira'): array
    {
        return [
            'key' => $key,
            'fields' => [
                Tickets::FIELD_STORY_POINTS => '5.0',
                'status' => [
                    'name' => 'In Progress',
                ],
                'summary' => 'The ticket title',
                'statuscategorychangedate' => '2019-06-15T10:35:00+00',
                'assignee' => [
                    'name' => 'Real Name',
                    'key' => $assigneeKey,
                    'emailAddress' => $email,
                    'displayName' => 'display.name.jira',
                ],
            ],
        ];
    }
}
