<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMessage;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotEnv->load();

$jiraClient = new JiraHttpClient(HttpClient::create([
    'auth_basic' => [getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD')],
]));

$ticketsInReview = $jiraClient->inReview(getenv('COMPANY_NAME'), 'Core Service Team ');
$ticketsInQA = $jiraClient->inQA(getenv('COMPANY_NAME'), 'Core Service Team ');

$response = (new SlackHttpClient(HttpClient::create([
    'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
])))->postToChannel(
    'UL84M0H9P',
    SlackMessage::fromJiraTickets($ticketsInQA, getenv('COMPANY_NAME'))
);

dd($response->getContent());
