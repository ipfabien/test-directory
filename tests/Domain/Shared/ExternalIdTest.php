<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared;

use App\Domain\Shared\ExternalId;
use PHPUnit\Framework\TestCase;

final class ExternalIdTest extends TestCase
{
    public function testFromStringAcceptsValidUuid(): void
    {
        $uuid       = '11111111-1111-1111-1111-111111111111';
        $externalId = ExternalId::fromString($uuid);

        self::assertSame($uuid, $externalId->toString());
    }

    public function testFromStringRejectsInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ExternalId::fromString('not-a-uuid');
    }

    public function testEqualsComparesUnderlyingValue(): void
    {
        $uuid = '11111111-1111-1111-1111-111111111111';

        $a = ExternalId::fromString($uuid);
        $b = ExternalId::fromString($uuid);
        $c = ExternalId::fromString('22222222-2222-2222-2222-222222222222');

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }
}


