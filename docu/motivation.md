# Motivation of this library

Wouldn't you like to know how long do your Jira tickets remain in some specific status? Well, the JiraApi 
tells you that, and therefore, we'll like to go one step further. You can define some threshold that will 
delimit the "max allow days" for a ticket to belong in a specific status. For example, in "TODO" it should 
be maxed 4 days, in "PROGRESS" max 3 days, and so on. If a ticket gets stuck and doesn't move along, so it 
remains in "TODO" for more than 4 days, or in "PROGRESS" more than 3 days, then a notification (via slack 
or email, for example) will be triggered.

That's the whole idea. To get informed about tickets that might be stuck or should require more attention 
(according to your fully customized threshold).

## How does it work

Jira has its owns HTTP API. It's called [`JQL`](https://www.atlassian.com/software/jira/guides/expand-jira/jql). 
The only things that you need are the `JIRA_API_LABEL` and the `JIRA_API_PASSWORD` 
[more info about how to get them here](https://id.atlassian.com/manage/api-tokens).

What you also will need for sure is your company (domain name under Jira) in order to be able to build the JQL queries.

Example: 
```
GET https://{company-name}.atlassian.net/rest/api/3/search
    ?jql=sprint in openSprints() ... AND NOT status changed after -Nd
```
![Example of JQL usage](https://i.ibb.co/vsVhCTx/Screenshot-2020-04-13-at-14-19-31.png)

This job is done by the class `JqlUrlBuilder`.

After obtaining all those tickets that should be "notified" we parse all into our domain models, and we group 
them per user, therefore it will be way easier to manipulate their data inside.

The `JiraConnector` class is in charge of sending the tickets (resulting from the Jira Response after being 
transform into our domain) to the different channels (such as Email, Slack, etc) in order to do whatever these 
channels want to do with them. Either send an email, send a slack notification, rendering in the terminal, 
or whatever the client at the end wants to do with this data.

As a response from `JiraConnector::handle()` we will receive an `array<string, ChannelResult>` which consist 
in the result per each individual channel, so we will be able to distinguish what happened (if any notification 
was sent or not) for each individual ticket on each individual channel.
