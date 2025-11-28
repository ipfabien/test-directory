<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared;

use App\Domain\Shared\Pagination;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    public function testCreateAppliesDefaults(): void
    {
        $pagination = Pagination::create(null, null);

        self::assertSame(1, $pagination->page());
        self::assertSame(20, $pagination->perPage());
        self::assertSame(0, $pagination->offset());
    }

    public function testCreateClampsPageAndPerPage(): void
    {
        $pagination = Pagination::create(0, 0);

        self::assertSame(1, $pagination->page());
        self::assertSame(1, $pagination->perPage());

        $pagination = Pagination::create(5, 1000);

        self::assertSame(5, $pagination->page());
        self::assertSame(100, $pagination->perPage());
        self::assertSame((5 - 1) * 100, $pagination->offset());
    }
}


