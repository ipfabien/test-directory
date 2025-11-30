<?php

declare(strict_types=1);

namespace App\Message\Event\Contact;

use App\Domain\Email\Email as DomainEmail;
use App\Domain\Email\SendEmail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Asynchronous handler that sends an email when a contact has been created.
 */
final class ContactCreatedHandler implements MessageHandlerInterface
{
    private SendEmail $sendEmail;

    public function __construct(SendEmail $sendEmail)
    {
        $this->sendEmail = $sendEmail;
    }

    public function __invoke(ContactCreated $event): void
    {
        $email = DomainEmail::create(
            $event->externalId(),
            $event->firstname(),
            $event->lastname(),
            $event->email()
        );

        $this->sendEmail->send($email);
    }
}
