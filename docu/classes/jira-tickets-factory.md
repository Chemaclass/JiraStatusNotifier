# JiraTicketsFactory

## What's this for?

By default, when you create a `new JiraHttpClient` defining only the `HttpClient` like:

```php
new JiraHttpClient(
    HttpClient::create([
       'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
    ])
);
```

You will use a `new JiraTicketsFactory` "without custom fields", which means no custom fields will be loaded in your domain `Ticket` class.

In Jira, you can define multiple custom fields which will have some key-name such as `customfield_10005`. You might want to access some of these fields but they might be different from one project to another. How could you access the, for example, the "Story Points field"? 


## Basic usage

Well, you might recognize this field from the "Jira Http Response" as `customfield_10005`. Then what you need to do is defining this field as the second argument from your `JiraHttpClient` object. For example:

```php
new JiraHttpClient(
    HttpClient::create([
       'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
    ]),
    new JiraTicketsFactory(['customfield_10005'])
);
```

Having this will allow you to get this information by `$ticket->customFields()['customfield_10005']`. For example: 

```twig
{% for ticket in tickets %}
    <b>Story Points</b>: {{ ticket.customFields['customfield_10005'] }}<br>
{% endfor %}
```

## Advanced usage: Using aliases

But, wait! Couldn't we use a better name for these custom fields? Yes. Instead of adding the custom field name, add as well the alias that you would like to use for that specific custom field. For example:

```php
new JiraHttpClient(
    HttpClient::create([
       'auth_basic' => [getenv('JIRA_API_LABEL'), getenv('JIRA_API_PASSWORD')],
    ]),
    new JiraTicketsFactory(['customfield_10005' => 'StoryPoints'])
);
```

And that's will be the `key`('StoryPoints') that will be used in order to store the value coming from 'customfield_10005'. Now you should be using that one instead. For example:

```twig
{% for ticket in tickets %}
    <b>Story Points</b>: {{ ticket.customFields['StoryPoints'] }}<br>
{% endfor %}
```

## Real working example

See some real examples in the `Email Channel Example`:

* [Console/Command](https://github.com/Chemaclass/JiraStatusNotifier/blob/master/examples/using-email-channel/console). Notice how the output uses the `output/channel-result.twig` template path. And it has the alias defined:

```php
new JiraTicketsFactory(['customfield_10005' => 'StoryPoints'])
```

* [Template](https://github.com/Chemaclass/JiraStatusNotifier/blob/master/examples/templates/email-template.twig). Notice how the template uses:
```twig
{% for ticket in tickets %}
  <!-- ... -->
  <b>Story Points</b>: {{ ticket.customFields['StoryPoints'] }}
{% endfor %}
```
