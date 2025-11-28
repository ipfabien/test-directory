<?php

declare(strict_types=1);

namespace App\Tests\Domain\Contact;

use App\Domain\Contact\CreateContact;
use PHPUnit\Framework\TestCase;

final class CreateContactTest extends TestCase
{
    private const MANAGER_ID = '11111111-1111-1111-1111-111111111111';

    public function testCreateValidContact(): void
    {
        $vo = CreateContact::create(
            'John',
            'Doe',
            'john.doe@example.com',
            self::MANAGER_ID,
            '+33123456789',
            'Note'
        );

        self::assertSame('John', $vo->firstname());
        self::assertSame('Doe', $vo->lastname());
        self::assertSame('john.doe@example.com', $vo->email());
        self::assertSame('+33123456789', $vo->phone());
        self::assertSame('Note', $vo->note());
        self::assertSame(self::MANAGER_ID, $vo->managerExternalId());
    }

    public function testCreateRejectsEmptyFirstname(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateContact::create(
            '',
            'Doe',
            'john.doe@example.com',
            self::MANAGER_ID
        );
    }

    public function testCreateRejectsInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateContact::create(
            'John',
            'Doe',
            'not-an-email',
            self::MANAGER_ID
        );
    }

    public function testCreateRejectsTooLongPhone(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateContact::create(
            'John',
            'Doe',
            'john.doe@example.com',
            self::MANAGER_ID,
            str_repeat('1', 33) // > 32
        );
    }

    public function testCreateRejectsTooLongNote(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateContact::create(
            'John',
            'Doe',
            'john.doe@example.com',
            self::MANAGER_ID,
            null,
            str_repeat('a', 2001)
        );
    }

    public function testCreateRejectsEmptyManagerExternalId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateContact::create(
            'John',
            'Doe',
            'john.doe@example.com',
            ''
        );
    }

    public function testCreateRejectsInvalidManagerExternalIdFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateContact::create(
            'John',
            'Doe',
            'john.doe@example.com',
            'not-a-uuid'
        );
    }
}


