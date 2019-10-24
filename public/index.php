<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMessage;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotEnv->load();

$jiraClient = new JiraHttpClient(HttpClient::create([
    'auth_basic' => [getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD')],
]));

$companyName = getenv('COMPANY_NAME');
$projectName = 'Core Service Team ';
$slackMapping = new SlackMapping(json_decode(getenv('SLACK_MAPPING_IDS'), true));

foreach (Board::MAX_DAYS_IN_STATUS as $statusName => $maxDays) {
    $tickets = $jiraClient->getTickets($statusName, $companyName, $projectName);

    foreach ($tickets as $ticket) {
        $slackClient = new SlackHttpClient(HttpClient::create([
            'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
        ]));

        $slackClient->postToChannel(
            $slackMapping->toSlackId($ticket->assignee()->name()),
            SlackMessage::fromJiraTicket($ticket, getenv('COMPANY_NAME'))
        );
    }
}

echo 'Take a look at Slack ;)' . PHP_EOL;
echo date('Y-m-d H:i:s.u');
