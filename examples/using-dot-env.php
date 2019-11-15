<?php
/**
 * This example demonstrates how to notify via Slack to the people assigned to the JIRA-tickets
 * using the ENV parameters (from the .env file)
 */
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Chemaclass\ScrumMaster\Command\IO\EchoOutput;
use Chemaclass\ScrumMaster\Command\SlackNotifierCommand;
use Chemaclass\ScrumMaster\Command\SlackNotifierInput;
use Chemaclass\ScrumMaster\Command\SlackNotifierOutput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Slack\SlackHttpClient;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(dirname(__DIR__));
$dotEnv->load();

if (!isset($_ENV['JIRA_API_LABEL'])
    || !isset($_ENV['JIRA_API_PASSWORD'])
    || !isset($_ENV['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'])
) {
    echo 'JIRA_API_LABEL, JIRA_API_PASSWORD and SLACK_BOT_USER_OAUTH_ACCESS_TOKEN keys are mandatory!';
    exit(1);
}

$command = new SlackNotifierCommand(
    new JiraHttpClient(HttpClient::create([
        'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
    ])),
    new SlackHttpClient(HttpClient::create([
        'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
    ]))
);

$command->execute(
    SlackNotifierInput::fromArray($_ENV),
    new SlackNotifierOutput(new EchoOutput())
);
