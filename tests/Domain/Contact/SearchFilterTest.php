<?php

declare(strict_types=1);

namespace App\Tests\Domain\Contact;

use App\Domain\Contact\SearchFilter;
use PHPUnit\Framework\TestCase;

final class SearchFilterTest extends TestCase
{
    public function testCreateStoresGivenValues(): void
    {
        $filter = SearchFilter::create('John', 'Doe', 'john.doe@example.com', '+33123456789');

        self::assertSame('John', $filter->firstname());
        self::assertSame('Doe', $filter->lastname());
        self::assertSame('john.doe@example.com', $filter->email());
        self::assertSame('+33123456789', $filter->phone());
    }

    public function testCreateAcceptsNulls(): void
    {
        $filter = SearchFilter::create(null, null, null, null);

        self::assertNull($filter->firstname());
        self::assertNull($filter->lastname());
        self::assertNull($filter->email());
        self::assertNull($filter->phone());
    }
}


