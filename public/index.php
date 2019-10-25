<?php
declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\ReadModel\CompanyProject;
use App\ScrumMaster\Jira\UrlFactory;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotEnv->load();

$jiraClient = new JiraHttpClient(
    HttpClient::create([
        'auth_basic' => [getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD')],
    ]),
    new SlackHttpClient(HttpClient::create([
        'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
    ])),
    new UrlFactory(new Board()),
    new CompanyProject(
        getenv('COMPANY_NAME'),
        $projectName = 'Core Service Team '
    )
);

$jiraClient->sendNotifications(
    new SlackMapping(json_decode(getenv('SLACK_MAPPING_IDS'), true)),
    new SlackMessage(new DateTimeImmutable())
);

print  sprintf('Finished at %s | Take a look at Slack ;)', date('Y-m-d H:i:s'));
