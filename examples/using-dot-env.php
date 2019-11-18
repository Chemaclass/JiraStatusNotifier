<?php
/**
 * This example demonstrates how to notify via Slack to the people assigned to the JIRA-tickets
 * using the ENV parameters (from the .env file)
 */
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Chemaclass\ScrumMaster\Command\IO\EchoOutput;
use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Command\NotifierOutput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Slack\MessageTemplate\SlackMessage;
use Chemaclass\ScrumMaster\Slack\SlackChannel;
use Chemaclass\ScrumMaster\Slack\SlackHttpClient;
use Chemaclass\ScrumMaster\Slack\SlackMapping;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(dirname(__DIR__));
$dotEnv->load();

$mandatoryKeys = [
    'JIRA_API_LABEL',
    'JIRA_API_PASSWORD',
    'SLACK_BOT_USER_OAUTH_ACCESS_TOKEN',
    'SLACK_MAPPING_IDS',
];

foreach ($mandatoryKeys as $mandatoryKey) {
    if (!isset($_ENV[$mandatoryKey])) {
        echo implode(', ', $mandatoryKeys) . 'keys are mandatory!';
        exit(1);
    }
}

$command = new NotifierCommand(
    new JiraHttpClient(HttpClient::create([
        'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
    ])),
    $channels = [
        new SlackChannel(
            new SlackHttpClient(HttpClient::create([
                'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
            ])),
            SlackMapping::jiraNameWithSlackId(json_decode(getenv('SLACK_MAPPING_IDS'), true)),
            SlackMessage::withTimeToDiff(new DateTimeImmutable())
        ),
    ]
);

$command->execute(
    NotifierInput::fromArray($_ENV),
    new NotifierOutput(new EchoOutput())
);
