<?php

declare(strict_types=1);

namespace App\Domain\Email;

interface SendEmail
{
    public function send(Email $email): void;
}
