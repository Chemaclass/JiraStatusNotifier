# Scrum Master

[![Build Status](https://travis-ci.org/Chemaclass/ScrumMaster.svg?branch=master)](https://travis-ci.org/Chemaclass/ScrumMaster)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

This tool will notify the person assigned a JIRA-ticket if the ticket remains in the same status for more than `N` days.

## Installation

Using composer: ```composer require chemaclass/scrum-master```

## Documentation

* Using [Slack](examples/using-slack-channel) as notification channel
* Using [Email](examples/using-email-channel) as notification channel
* Using [Cli](examples/using-cli-channel) to render the tickets for each assignee without notifying anybody
