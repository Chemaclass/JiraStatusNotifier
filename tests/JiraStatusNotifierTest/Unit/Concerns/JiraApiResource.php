<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifierTests\Unit\Concerns;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait JiraApiResource
{
    /**
     * @return HttpClientInterface|MockObject
     */
    private function mockJiraClient(array $issues)
    {
        $jiraResponse = $this->createMock(ResponseInterface::class);
        $jiraResponse->method('toArray')->willReturn(['issues' => $issues]);

        /** @var HttpClientInterface|MockObject $jiraClient */
        $jiraClient = $this->createMock(HttpClientInterface::class);
        $jiraClient->method('request')->willReturn($jiraResponse);

        return $jiraClient;
    }

    private function createAJiraIssueAsArray(
        string $assigneeId,
        string $key,
        string $statusName = 'In Progress'
    ): array {
        return [
            'key' => $key,
            'fields' => [
                'status' => [
                    'name' => $statusName,
                ],
                'summary' => 'The ticket title',
                'statuscategorychangedate' => '2019-06-15T10:35:00+00',
                'assignee' => [
                    'name' => 'Real Name',
                    'accountId' => $assigneeId,
                    'displayName' => 'display.name.jira',
                ],
            ],
        ];
    }
}
