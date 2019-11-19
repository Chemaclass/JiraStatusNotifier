# Scrum Master

This tool will notify the person assigned a JIRA-ticket if the ticket remains in the same status for more than `N` days.

## Mandatory parameters

These are the required parameters in order to connect to JIRA and fetch all the tickets from the board.

### COMPANY_NAME

Needed in order to build the JIRA link to browse to a specific ticket like:

`https://{$companyName}.atlassian.net/browse/{$ticket->key()}`

### JIRA_PROJECT_NAME

Needed to build the JQL Get query to fetch the information from the JIRA API.

[Official documentation](https://confluence.atlassian.com/jirasoftwarecloud/advanced-searching-764478330.html)

### JIRA_API_LABEL && JIRA_API_PASSWORD

Mandatory credentials to "auth basic" the request to JIRA

You can create your own API token here: https://id.atlassian.com/manage/api-tokens

### DAYS_FOR_STATUS

Value example: `'{"To Do":6,"In Progress":4,"In Review":1,"IN QA":2,"Verified":1}'`

This JSON consist of an array of `<string:int>` where

* the `string` is the status name from the JIRA board
* the `int` is the max days that a ticket could be in that status.

In case the JIRA ticket is in that status for more days than it should,
a slack notification will be sent to the responsible person.

### JIRA_USERS_TO_IGNORE (optional)

Value example: `'["any.jira.user.key","another.jira.user.key"]'`

Prevent sending any notifications to the users listed by their jira user-key.

This is optional. Default value: `'[]'`
