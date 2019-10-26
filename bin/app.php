<?php
declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\ScrumMaster\Jira\Board;
use App\ScrumMaster\Jira\JiraHttpClient;
use App\ScrumMaster\Jira\JqlUrlBuilder;
use App\ScrumMaster\Jira\JqlUrlFactory;
use App\ScrumMaster\Jira\ReadModel\Company;
use App\ScrumMaster\Slack\SlackHttpClient;
use App\ScrumMaster\Slack\SlackMapping;
use App\ScrumMaster\Slack\SlackMessage;
use App\ScrumMaster\SlackNotifier;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotEnv->load();

$jiraBoard = new Board(json_decode(getenv('DAYS_FOR_STATUS'), true));

$company = Company::withNameAndProject(
    getenv('COMPANY_NAME'),
    getenv('JIRA_PROJECT_NAME')
);

$slackNotifier = new SlackNotifier(
    $jiraBoard,
    new JiraHttpClient(
        HttpClient::create([
            'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
        ]),
        new JqlUrlFactory($jiraBoard, JqlUrlBuilder::inOpenSprints($company))
    ),
    new SlackHttpClient(HttpClient::create([
        'auth_bearer' => getenv('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'),
    ]))
);

$slackNotifier->sendNotifications(
    $company,
    SlackMapping::jiraNameWithSlackId(
        json_decode(getenv('SLACK_MAPPING_IDS'), true)
    ),
    SlackMessage::withTimeToDiff(new DateTimeImmutable())
);

print sprintf('Finished at %s | Take a look at Slack ;)', date('Y-m-d H:i:s'));
