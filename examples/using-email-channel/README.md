# Email Channel

This project is using the `symfony/mailer` which provides a really good flexibility in order to use
a [3rd party transport](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport).

3rd parties such as:

- Amazon SES -> `composer require symfony/amazon-mailer`
- Gmail -> `composer require symfony/google-mailer`
- MailChimp -> `composer require symfony/mailchimp-mailer`
- Mailgun -> `composer require symfony/mailgun-mailer`
- Postmark -> `composer require symfony/postmark-mailer`
- SendGrid -> `composer require symfony/sendgrid-mailer`

## Parameters

Apart from the [mandatory parameters](../../docu/README.md), you will need:

#### MAILER_USERNAME

Value example: `your@email.com`

#### MAILER_PASSWORD

Value example: `1he-p@$w0rd`

#### JIRA_IDS_TO_EMAIL

Value example: `{'jira.user.account_id': 'his/her@email.com', ...}`

It will set the email destination from the user which will receive the email as a notification.

## References

* [Symfony Mailer Documentation](https://symfony.com/doc/current/mailer.html)

## Usage

1. Create the `.env` file from its dist version: `cp .env.dist .env`
2. Update the `.env` values
3. Execute it: `php console` or `./console`
