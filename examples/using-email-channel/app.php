<?php
/**
 * This example demonstrates how to notify via Slack to the people assigned to the JIRA-tickets
 * using the ENV parameters (from the .env file)
 */
declare(strict_types=1);

require dirname(__DIR__) . '/../vendor/autoload.php';

use Chemaclass\ScrumMaster\Channel\Email;
use Chemaclass\ScrumMaster\Command\IO\EchoOutput;
use Chemaclass\ScrumMaster\Command\NotifierCommand;
use Chemaclass\ScrumMaster\Command\NotifierInput;
use Chemaclass\ScrumMaster\Command\NotifierOutput;
use Chemaclass\ScrumMaster\Jira\JiraHttpClient;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__);
$dotEnv->load();

$mandatoryKeys = [
    'COMPANY_NAME',
    'JIRA_PROJECT_NAME',
    'JIRA_API_LABEL',
    'JIRA_API_PASSWORD',
    'DAYS_FOR_STATUS',
    'MAILER_HOST',
    'MAILER_PORT',
    'MAILER_ENCRYPTION',
    'MAILER_USERNAME',
    'MAILER_PASSWORD',
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
        new Email\Channel(
            new Email\Client(new \Swift_Mailer(
                (new \Swift_SmtpTransport(getenv('MAILER_HOST'), getenv('MAILER_PORT'), getenv('MAILER_ENCRYPTION')))
                    ->setUsername(getenv('MAILER_USERNAME'))
                    ->setPassword(getenv('MAILER_PASSWORD'))
            )),
            Email\MessageGenerator::withTimeToDiff(new DateTimeImmutable())
        ),
    ]
);

$result = $command->execute(NotifierInput::fromArray($_ENV));
$output = new NotifierOutput(new EchoOutput());
$output->write($result);
