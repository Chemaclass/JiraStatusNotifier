# Scrum Master   

This tool will notify the person assigned a JIRA-ticket if the ticket remains in the same status for more than `N` days.

[![Build Status](https://travis-ci.org/Chemaclass/ScrumMaster.svg?branch=master)](https://travis-ci.org/Chemaclass/ScrumMaster)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/ScrumMaster/?branch=master)

## ENV Variables

### COMPANY_NAME

Needed in order to build the JIRA link to browse to a specific ticket like:

`https://{$companyName}.atlassian.net/browse/{$ticket->key()}`

### JIRA_PROJECT_NAME

Needed to build the JQL Get query to fetch the information from the JIRA API.

[Official documentation](https://confluence.atlassian.com/jirasoftwarecloud/advanced-searching-764478330.html)

### JIRA_API_LABEL && JIRA_API_PASSWORD

Mandatory credentials to "auth basic" the request to JIRA

You can create your own API token here: https://id.atlassian.com/manage/api-tokens

### SLACK_BOT_USER_OAUTH_ACCESS_TOKEN

You can create your own Slack App here: https://api.slack.com/apps (inside "Oauth & Permissions")

### SLACK_MAPPING_IDS

Value example: `'{"fallback":"slack.group.id", "jira.person.id":"slack.member.id"}'`

It is the mapping between the "person.id" from JIRA to the "channel name" or "person member_id" in slack .

> Important: in case you want to post something using a slack-bot into a slack channel, you have to
allow first this bot (as an app) to publish messages into that channel:
`Channel > Configuration > Add an app`

### DAYS_FOR_STATUS

Value example: `'{"To Do":6,"In Progress":4,"In Review":1,"IN QA":2,"Verified":1}'`

This JSON consist of an array of `<string:int>` where

* the `string` is the status name from the JIRA board
* the `int` is the max days that a ticket could be in that status.

In case the JIRA ticket is in that status for more days than it should,
a slack notification will be sent to the responsible person.
