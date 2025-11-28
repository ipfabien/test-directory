<?php

declare(strict_types=1);

namespace App\Tests\Domain\Contact;

use App\Domain\Contact\ContactList;
use App\Domain\Contact\ContactSummary;
use App\Domain\Shared\ExternalId;
use PHPUnit\Framework\TestCase;

final class ContactSummaryAndListTest extends TestCase
{
    public function testContactSummaryNormalizeAndDenormalize(): void
    {
        $externalId = ExternalId::fromString('11111111-1111-1111-1111-111111111111');

        $summary = ContactSummary::create(
            $externalId,
            'John',
            'Doe',
            'john.doe@example.com',
            '+33123456789'
        );

        $data    = $summary->normalize();
        $copy    = ContactSummary::denormalize($data);

        self::assertSame($summary->externalId()->toString(), $copy->externalId()->toString());
        self::assertSame($summary->firstname(), $copy->firstname());
        self::assertSame($summary->lastname(), $copy->lastname());
        self::assertSame($summary->email(), $copy->email());
        self::assertSame($summary->phone(), $copy->phone());
    }

    public function testContactListNormalizeAndDenormalize(): void
    {
        $summary1 = ContactSummary::create(
            ExternalId::fromString('11111111-1111-1111-1111-111111111111'),
            'John',
            'Doe',
            'john.doe@example.com',
            null
        );

        $summary2 = ContactSummary::create(
            ExternalId::fromString('22222222-2222-2222-2222-222222222222'),
            'Jane',
            'Doe',
            'jane.doe@example.com',
            '+33123456789'
        );

        $list = new ContactList($summary1, $summary2);

        $normalized = $list->normalize();
        $copy       = ContactList::denormalize($normalized);

        self::assertSame(2, \count($copy));
        self::assertSame($summary1->email(), $normalized[0]['email']);
        self::assertSame($summary2->email(), $normalized[1]['email']);
    }
}


