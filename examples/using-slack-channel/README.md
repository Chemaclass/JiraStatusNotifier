# Slack Channel

## Parameters

Apart from the [mandatory parameters](../../docu/README.md), you will need:

#### SLACK_BOT_USER_OAUTH_ACCESS_TOKEN

You can create your own Slack App here: https://api.slack.com/apps (inside "Oauth & Permissions")

#### SLACK_MAPPING_IDS

Value example: `'{"fallback":"slack.group.id", "jira.account_id":"slack.member.id", ...}'`

It is the mapping between the "person.id" from JIRA to the "channel name" or "person member_id" in slack .

> Important: in case you want to post something using a slack-bot into a slack channel, you have to
allow first this bot (as an app) to publish messages into that channel:
`Channel > Configuration > Add an app`

## Usage

1. Create the `.env` file from its dist version: `cp .env.dist .env`
2. Update the `.env` values
3. Execute it: `php console` or `./console`
