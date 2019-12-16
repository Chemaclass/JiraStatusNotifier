<?php
/**
 * This example demonstrates how to notify via Email and Slack
 * using the ENV parameters (from the .env file)
 */
declare(strict_types=1);

require dirname(__DIR__) . '/../vendor/autoload.php';

use Chemaclass\ScrumMaster\Channel\Email;
use Chemaclass\ScrumMaster\Channel\Email\ByPassEmail;
use Chemaclass\ScrumMaster\Channel\Slack;
use Chemaclass\ScrumMaster\IO\EchoOutput;
use Chemaclass\ScrumMaster\IO\NotifierInput;
use Chemaclass\ScrumMaster\IO\NotifierOutput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Chemaclass\ScrumMaster\Notifier;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;

$dotEnv = Dotenv\Dotenv::create(__DIR__);
$dotEnv->load();

$mandatoryKeys = [
    'COMPANY_NAME',
    'JIRA_PROJECT_NAME',
    'JIRA_API_LABEL',
    'JIRA_API_PASSWORD',
    'JIRA_USERS_TO_IGNORE',
    'DAYS_FOR_STATUS',
    'MAILER_USERNAME',
    'MAILER_PASSWORD',
    'SLACK_BOT_USER_OAUTH_ACCESS_TOKEN',
    'SLACK_MAPPING_IDS',
];

foreach ($mandatoryKeys as $mandatoryKey) {
    if (!isset($_ENV[$mandatoryKey])) {
        echo implode(', ', $mandatoryKeys) . 'keys are mandatory!';
        exit(1);
    }
}

$jiraHttpClient = new JiraHttpClient(HttpClient::create([
    'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
]));

$channels = [
    new Email\Channel(
        new Mailer(new GmailSmtpTransport(getenv('MAILER_USERNAME'), getenv('MAILER_PASSWORD'))),
        Email\MessageGenerator::withTimeToDiff(new DateTimeImmutable()),
        new Email\AddressGenerator((new ByPassEmail())
            ->setSendEmailsToAssignee(false)
            ->setSendCopyTo(getenv('MAILER_USERNAME')))
    ),
    new Slack\Channel(
        new Slack\HttpClient(HttpClient::create([
            'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
        ])),
        Slack\JiraMapping::jiraNameWithSlackId(json_decode(getenv('SLACK_MAPPING_IDS'), true)),
        Slack\MessageGenerator::withTimeToDiff(new DateTimeImmutable())
    ),
];

$notifier = new Notifier($jiraHttpClient, $channels);

$result = $notifier->notify(NotifierInput::new(
    getenv(NotifierInput::COMPANY_NAME),
    getenv(NotifierInput::JIRA_PROJECT_NAME),
    json_decode(getenv(NotifierInput::DAYS_FOR_STATUS), true),
    json_decode(getenv(NotifierInput::JIRA_USERS_TO_IGNORE), true)
));

$output = new NotifierOutput(new EchoOutput());
$output->write($result);
