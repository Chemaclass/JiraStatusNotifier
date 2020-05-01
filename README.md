# Jira Status Notifier

[![Build Status](https://travis-ci.org/Chemaclass/JiraStatusNotifier.svg?branch=master)](https://travis-ci.org/Chemaclass/JiraStatusNotifier)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/JiraStatusNotifier/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/JiraStatusNotifier/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/JiraStatusNotifier/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/JiraStatusNotifier/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

This tool will notify the person assigned a JIRA-ticket if the ticket remains in the same status for more than `N` days.

## Installation as vendor

Using composer: ```composer require chemaclass/jira-status-notifier```

## Development and contribution

### Requirements

#### GNU Make 4.+ (for Makefile) [Install for Mac](https://stackoverflow.com/questions/43175529/updating-make-version-4-1-on-mac) | Optional

Some make tasks to execute commands inside the docker container such:

* `make bash` -> access into the bash
* `make csfix` -> run the code style fixer (`.php_cs`)
* `make composer ARGS="install"` -> run composer
* `make tests ARGS="--filter AddressGenerator"` -> run PHPUnit

### Your first try!

1. Clone/Fork the project and `cd` inside the repository
2. `docker-compose up`
3. `make bash` or `docker exec -ti -u dev jira_status_notifier_php bash` 
4. `cd examples/using-cli-channel`
5. `cp .env.dist .env`
6. Update the [`.env` values](docu/mandatory-parameters.md)
7. `php console` or `./console`

## Documentation

* Using [Slack](examples/using-slack-channel) as notification channel
* Using [Email](examples/using-email-channel) as notification channel
* Using [Cli](examples/using-cli-channel) to render the tickets for each assignee without notifying anybody

## Basic Example

```php
<?php declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Chemaclass\JiraStatusNotifier\Channel\{Cli, Email, Slack};
use Chemaclass\JiraStatusNotifier\IO\JiraConnectorInput;
use Chemaclass\JiraStatusNotifier\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\JiraConnector;
use Symfony\Component\HttpClient\HttpClient;

$jiraConnector = new JiraConnector(
    new JiraHttpClient(
        HttpClient::create(['auth_basic' => ['jiraAPiLabel', 'jiraApiPassword']])
    ), 
    new Slack\Channel(/* ... */),
    new Email\Channel(/* ... */),
    new Cli\Channel()
);

$result = $jiraConnector->handle(JiraConnectorInput::new(
    'company-name',
    'Jira Project Name',
    ['To Do' => 3, 'In Progress' => 2, 'In Review' => 1]
));
```

