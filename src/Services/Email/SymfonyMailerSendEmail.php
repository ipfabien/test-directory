<?php

declare(strict_types=1);

namespace App\Services\Email;

use App\Domain\Email\Email as DomainEmail;
use App\Domain\Email\SendEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Adapter that sends emails using Symfony Mailer.
 *
 * In this project, emails are sent to a fixed admin address and visible in Mailhog.
 */
final class SymfonyMailerSendEmail implements SendEmail
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(DomainEmail $email): void
    {
        $symfonyEmail = (new Email())
            ->from('no-reply@test.test')
            ->to('admin@test.test')
            ->subject('A new contact has been created')
            ->text(sprintf(
                "New contact created: %s %s (external_id: %s)\nEmail: %s\n",
                $email->firstname(),
                $email->lastname(),
                $email->externalId(),
                $email->email()
            ));

        $this->mailer->send($symfonyEmail);
    }
}
