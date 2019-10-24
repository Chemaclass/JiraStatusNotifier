<?php
require_once '../vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;

$username = "j.valera@sevensenders.com";
$password = "ZzwVdEtyrDH3bT1WpXmG84DF";

$client = HttpClient::create([
    'auth_basic' => [$username, $password],
]);

$url = 'https://sevensenders.atlassian.net/rest/api/3/search?jql=sprint in openSprints()';
$url .= ' AND project IN ("Core Service Team ") AND status = Verified AND NOT status changed after -1d';
$response = $client->request('GET', $url);
$content = $response->toArray();
dump($content);
