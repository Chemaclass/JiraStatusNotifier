<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\ScrumMaster\JiraTickets;
use App\ScrumMaster\JqlUrlBuilder;
use Symfony\Component\HttpClient\HttpClient;

$dotEnv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotEnv->load();

$client = HttpClient::create([
    'auth_basic' => [getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD')],
]);

$url = JqlUrlBuilder::inOpenSprints()
    ->inProject('Core Service Team ')
    ->withStatus("In Review")
    ->statusDidNotChangeSinceDays(1)
    ->build();

$response = $client->request('GET', $url);
$content = $response->toArray();
$jiraTickets = JiraTickets::fromJira($content);
dump($jiraTickets);
dd($content);

