# Scrum Master   

This tool will notify the assigner person of a JIRA ticket if the ticket
remains in the same status for more than `N` days.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/Chemaclass/scrum-master/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)


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

It's mapping between the "person.id" from JIRA to the "channel name" or "person member_id" in slack .

Extra: in case you want to post something using a slack bot into a slack channel, you have to
first allow this bot (as an app) to publish messages into that channel:

`Channel > Configuration > Add an app`

### DAYS_FOR_STATUS

Value example: `'{"To Do":6,"In Progress":4,"In Review":1,"IN QA":2,"Verified":1}'`

This json consist as array of `<string:int>` where the `string` is the status name
from JIRA, and the `int` is the max days that a ticket should be in that status.
Otherwise, it will trigger the slack notification to the responsible person.
