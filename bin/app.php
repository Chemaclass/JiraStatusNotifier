<?php
declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\JqlUrlFactory;
use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use App\ScrumMaster\SlackNotifier;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotEnv->load();

$jiraBoard = new Board([
    'To Do' => 6,
    'Blocked' => 7,
    'In Progress' => 4,
    'In Review' => 1,
    'Ready for QA' => 1,
    'IN QA' => 2,
    'Verified' => 1,
    'Ready For RC' => 1,
    'IN RC' => 3,
]);

$slackNotifier = new SlackNotifier(
    $jiraBoard,
    new JiraHttpClient(
        HttpClient::create([
            'auth_basic' => [getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD')],
        ]),
        new JqlUrlFactory($jiraBoard)
    ),
    new SlackHttpClient(HttpClient::create([
        'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
    ]))
);

$slackNotifier->sendNotifications(
    new CompanyProject(
        getenv('COMPANY_NAME'),
        $projectName = 'Core Service Team '
    ),
    new SlackMapping(json_decode(getenv('SLACK_MAPPING_IDS'), true)),
    new SlackMessage(new DateTimeImmutable())
);

print sprintf('Finished at %s | Take a look at Slack ;)', date('Y-m-d H:i:s'));
