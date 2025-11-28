<?php

declare(strict_types=1);

namespace App\Tests\Repository\Dbal;

use App\Domain\Contact\Contact;
use App\Domain\Contact\ContactList;
use App\Domain\Contact\CreateContact;
use App\Domain\Contact\SearchFilter;
use App\Domain\Shared\ExternalId;
use App\Domain\Shared\Pagination;
use App\Services\Repository\Dbal\DBALContactRepository;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\NotFoundException;
use App\Tests\Database\TransactionalDbalTestCase;

final class DBALContactRepositoryTest extends TransactionalDbalTestCase
{
    private DBALContactRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DBALContactRepository($this->connection);
    }

    public function testCreateAndFindContactWithManager(): void
    {
        $email            = 'john.doe.' . uniqid('', true) . '@example.com';
        $managerExternalId = '11111111-1111-1111-1111-111111111111';

        $create = CreateContact::create(
            'John',
            'Doe',
            $email,
            $managerExternalId,
            '+33123456789',
            'Test contact'
        );

        $externalId = $this->repository->create($create);

        $contact = $this->repository->find($externalId);

        self::assertInstanceOf(Contact::class, $contact);
        self::assertSame($email, $contact->email());
        self::assertNotNull($contact->manager());
        self::assertTrue($contact->manager()->externalId()->equals(ExternalId::fromString($managerExternalId)));
    }

    public function testCreateThrowsBadRequestOnDuplicateEmail(): void
    {
        $this->expectException(BadRequestException::class);

        $email            = 'jane.doe.' . uniqid('', true) . '@example.com';
        $managerExternalId = '11111111-1111-1111-1111-111111111111';

        $first = CreateContact::create('Jane', 'Doe', $email, $managerExternalId, null, null);
        $this->repository->create($first);

        $second = CreateContact::create('Jane', 'Doe', $email, $managerExternalId, null, null);
        $this->repository->create($second);
    }

    public function testFindThrowsNotFoundForUnknownExternalId(): void
    {
        $this->expectException(NotFoundException::class);

        $this->repository->find(ExternalId::fromString('99999999-9999-9999-9999-999999999999'));
    }

    public function testFindByManagerReturnsContacts(): void
    {
        $managerExternalId = ExternalId::fromString('11111111-1111-1111-1111-111111111111');

        // ensure at least one contact for this manager
        $email = 'manager.list.' . uniqid('', true) . '@example.com';
        $create = CreateContact::create('List', 'Contact', $email, $managerExternalId->toString(), null, null);
        $this->repository->create($create);

        $contacts = $this->repository->findByManager($managerExternalId);

        self::assertInstanceOf(ContactList::class, $contacts);
        self::assertGreaterThanOrEqual(1, \count($contacts));
    }

    public function testFindManagerForContactReturnsManager(): void
    {
        $managerExternalId = '11111111-1111-1111-1111-111111111111';
        $email             = 'manager.for.contact.' . uniqid('', true) . '@example.com';

        $create     = CreateContact::create('Mgr', 'Link', $email, $managerExternalId, null, null);
        $externalId = $this->repository->create($create);

        $manager = $this->repository->findManagerForContact($externalId);

        self::assertTrue($manager->externalId()->equals(ExternalId::fromString($managerExternalId)));
    }

    public function testSearchWithFilterAndPagination(): void
    {
        $managerExternalId = '11111111-1111-1111-1111-111111111111';

        // Insert two contacts with distinct names
        $createOne = CreateContact::create(
            'AliceSearch',
            'Alpha',
            'alice.' . uniqid('', true) . '@example.com',
            $managerExternalId,
            null,
            null
        );
        $this->repository->create($createOne);

        $createTwo = CreateContact::create(
            'BobSearch',
            'Beta',
            'bob.' . uniqid('', true) . '@example.com',
            $managerExternalId,
            null,
            null
        );
        $this->repository->create($createTwo);

        $filter     = SearchFilter::create('AliceSearch', null, null, null);
        $pagination = Pagination::create(1, 10);

        $results = $this->repository->search($filter, $pagination);

        self::assertGreaterThanOrEqual(1, \count($results));

        foreach ($results as $summary) {
            self::assertStringContainsString('AliceSearch', $summary->firstname());
        }
    }
}


