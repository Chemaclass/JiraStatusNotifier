<?php
/**
 * This example demonstrates how to notify via Slack to the people assigned to the JIRA-tickets
 * using the ENV parameters (from the .env file)
 */
declare(strict_types=1);

require dirname(__DIR__) . '/../vendor/autoload.php';

use Chemaclass\ScrumMaster\Channel\Email;
use Chemaclass\ScrumMaster\Channel\Slack;
use Chemaclass\ScrumMaster\Command\IO\EchoOutput;
use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Command\NotifierOutput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__);
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
//        new Slack\Channel(
//            new Slack\HttpClient(HttpClient::create([
//                'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
//            ])),
//            Slack\JiraMapping::jiraNameWithSlackId(json_decode(getenv('SLACK_MAPPING_IDS'), true)),
//            Slack\MessageGenerator::withTimeToDiff(new DateTimeImmutable())
//        ),
        new Email\Channel(
            Email\Client::withMailer(new \Swift_Mailer(
                (new \Swift_SmtpTransport(getenv('MAILER_URL_HOST')))
                    ->setUsername(getenv('MAILER_URL_USERNAME'))
                    ->setPassword(getenv('MAILER_URL_PASSWORD'))
            )),
            Email\MessageGenerator::withTimeToDiff(new DateTimeImmutable())
        ),
    ]
);

$result = $command->execute(NotifierInput::fromArray($_ENV));
$output = new NotifierOutput(new EchoOutput());
$output->write($result);
