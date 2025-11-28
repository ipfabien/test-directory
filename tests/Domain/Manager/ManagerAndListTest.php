<?php

declare(strict_types=1);

namespace App\Tests\Domain\Manager;

use App\Domain\Manager\Manager;
use App\Domain\Manager\ManagerList;
use App\Domain\Manager\ManagerSummary;
use App\Domain\Shared\ExternalId;
use PHPUnit\Framework\TestCase;

final class ManagerAndListTest extends TestCase
{
    public function testManagerNormalizeAndDenormalize(): void
    {
        $externalId = ExternalId::fromString('11111111-1111-1111-1111-111111111111');

        $manager = Manager::create($externalId, 'Alice', 'Manager');

        $data = $manager->normalize();
        $copy = Manager::denormalize($data);

        self::assertSame($manager->externalId()->toString(), $copy->externalId()->toString());
        self::assertSame($manager->firstname(), $copy->firstname());
        self::assertSame($manager->lastname(), $copy->lastname());
    }

    public function testManagerSummaryNormalizeAndDenormalize(): void
    {
        $externalId = ExternalId::fromString('22222222-2222-2222-2222-222222222222');

        $summary = ManagerSummary::create($externalId, 'Bob', 'Supervisor');

        $data = $summary->normalize();
        $copy = ManagerSummary::denormalize($data);

        self::assertSame($summary->externalId()->toString(), $copy->externalId()->toString());
        self::assertSame($summary->firstname(), $copy->firstname());
        self::assertSame($summary->lastname(), $copy->lastname());
    }

    public function testManagerListNormalizeAndDenormalize(): void
    {
        $summary1 = ManagerSummary::create(
            ExternalId::fromString('11111111-1111-1111-1111-111111111111'),
            'Alice',
            'Manager'
        );

        $summary2 = ManagerSummary::create(
            ExternalId::fromString('22222222-2222-2222-2222-222222222222'),
            'Bob',
            'Supervisor'
        );

        $list = new ManagerList($summary1, $summary2);

        $normalized = $list->normalize();
        $copy       = ManagerList::denormalize($normalized);

        self::assertSame(2, \count($copy));
        self::assertSame($summary1->firstname(), $normalized[0]['firstname']);
        self::assertSame($summary2->firstname(), $normalized[1]['firstname']);
    }
}


