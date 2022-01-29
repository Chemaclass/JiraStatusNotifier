<?php

declare(strict_types=1);

namespace Chemaclass\JiraStatusNotifier;

use Chemaclass\JiraStatusNotifier\Domain\Channel\ChannelInterface;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Cli\CliChannel;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Email;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Email\EmailChannel;
use Chemaclass\JiraStatusNotifier\Domain\Channel\MessageGenerator;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Slack;
use Chemaclass\JiraStatusNotifier\Domain\Channel\Slack\SlackChannel;
use Chemaclass\JiraStatusNotifier\Domain\IO\EchoOutput;
use Chemaclass\JiraStatusNotifier\Domain\IO\NotifierOutputRenderer;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraHttpClient;
use Chemaclass\JiraStatusNotifier\Domain\Jira\JiraTicketsFactory;
use Chemaclass\JiraStatusNotifier\Domain\JiraConnector;
use DateTimeImmutable;
use Gacela\Framework\AbstractFactory;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @method JiraStatusNotifierConfig getConfig()
 */
final class JiraStatusNotifierFactory extends AbstractFactory
{
    /**
     * @param list<class-string> $channels
     */
    public function createJiraConnector(array $channels): JiraConnector
    {
        return new JiraConnector(
            $this->createJiraHttpClient(),
            $this->getConfig()->getJiraConnectorInput(),
            $this->createChannels($channels)
        );
    }

    private function createJiraHttpClient(): JiraHttpClient
    {
        return new JiraHttpClient(
            HttpClient::create([
                'auth_basic' => [
                    $this->getConfig()->getJiraApiLabel(),
                    $this->getConfig()->getJiraApiPassword(),
                ],
            ]),
            new JiraTicketsFactory($this->getConfig()->getCustomFields())
        );
    }

    /**
     * @param list<class-string> $channelNames
     *
     * @return list<ChannelInterface>
     */
    private function createChannels(array $channelNames): array
    {
        $channels = [];

        foreach ($channelNames as $channelName) {
            $channels[] = match ($channelName) {
                CliChannel::class => $this->createCliChannel(),
                EmailChannel::class => $this->createEmailChannel(),
                SlackChannel::class => $this->createSlackChannel(),
                default => throw new InvalidArgumentException('Unknown channel with name: ' . $channelName),
            };
        }

        return $channels;
    }

    private function createCliChannel(): ChannelInterface
    {
        return new CliChannel();
    }

    private function createEmailChannel(): ChannelInterface
    {
        return new EmailChannel(
            new Mailer(
                new GmailSmtpTransport(
                    $this->getConfig()->getMailerUsername(),
                    $this->getConfig()->getMailerPassword(),
                )
            ),
            new MessageGenerator(
                new DateTimeImmutable(),
                $this->createTwig(),
                $this->getConfig()->getEmailTemplateName()
            ),
            new Email\AddressGenerator($this->getConfig()->getJiraIdsToEmail())
        );
    }

    private function createSlackChannel(): ChannelInterface
    {
        return new SlackChannel(
            new Slack\HttpClient(
                HttpClient::create([
                    'auth_bearer' => $this->getConfig()->getSlackBotUserOauthAccessToken(),
                ])
            ),
            Slack\JiraMapping::jiraNameWithSlackId($this->getConfig()->getSlackMappingIds()),
            new MessageGenerator(
                new DateTimeImmutable(),
                $this->createTwig(),
                $this->getConfig()->getSlackTemplateName()
            )
        );
    }

    private function createTwig(): Environment
    {
        return new Environment(
            new FilesystemLoader($this->getConfig()->getTemplatesDirectory())
        );
    }

    public function createNotifierOutputRenderer(): NotifierOutputRenderer
    {
        return new NotifierOutputRenderer(
            $this->createEchoOutput(),
            $this->createTwig(),
            $this->getConfig()->getOutputRendererTemplateName()
        );
    }

    private function createEchoOutput(): EchoOutput
    {
        return new EchoOutput();
    }
}
