# Jira Status Notifier

[![Build Status](https://travis-ci.org/Chemaclass/JiraStatusNotifier.svg?branch=master)](https://travis-ci.org/Chemaclass/JiraStatusNotifier)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

This tool will notify the person assigned a JIRA-ticket if the ticket remains in the same status for more than `N` days.

## Installation as vendor

Using composer: ```composer require chemaclass/jira-status-notifier```

## Development and contribution

Requirements: PHP >=8.0

1. Fork and clone
2. composer install

### Composer scripts

```
composer test-all -> quality, phpunit
composer quality  -> csrun, psalm, phpstan
composer phpunit  -> test-unit, test-functional
```

See more in `composer.json`

## Documentation

* Using [Slack](examples/using-slack-channel) as notification channel
* Using [Email](examples/using-email-channel) as notification channel
* Using [Cli](examples/using-cli-channel) to render the tickets for each assignee without notifying anybody

## Basic Example

```php
$facade = new JiraStatusNotifierFacade();

$result = $facade->handle([
    CliChannel::class,
    SlackChannel::class,
    EmailChannel::class,
]);

$facade->renderOutput($result);
```
