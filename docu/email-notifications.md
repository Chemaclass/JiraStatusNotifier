# Email Channel

See an [implementation example](../examples/using-email-channel/app.php)

This project is using the `symfony/mailer` which provides a really good flexibility in order
to use a [3rd party transport](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport).

3rd parties such as:

- Amazon SES -> `composer require symfony/amazon-mailer`
- Gmail -> `composer require symfony/google-mailer`
- MailChimp -> `composer require symfony/mailchimp-mailer`
- Mailgun -> `composer require symfony/mailgun-mailer`
- Postmark -> `composer require symfony/postmark-mailer`
- SendGrid -> `composer require symfony/sendgrid-mailer`

## Parameters

Apart of the [mandatory parameters](README.md), you will need:

#### MAILER_USERNAME

Value example: `your@email.com`

#### MAILER_PASSWORD

Value example: `1he-p@$w0rd`

## References

* [Symfony Mailer Documentation](https://symfony.com/doc/current/mailer.html)
