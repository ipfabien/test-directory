<?php

declare(strict_types=1);

namespace App\Services\Repository\Dbal;

use App\Domain\Contact\Contact;
use App\Domain\Contact\ContactList;
use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\ContactSummary;
use App\Domain\Contact\CreateContact;
use App\Domain\Contact\SearchFilter;
use App\Domain\Manager\Manager;
use App\Domain\Shared\ExternalId;
use App\Domain\Shared\Pagination;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final class DBALContactRepository implements ContactRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function create(CreateContact $contact): ExternalId
    {
        $externalId = ExternalId::fromString(Uuid::v4()->toRfc4122());
        $now        = new \DateTimeImmutable();

        // Resolve manager_id from provided manager externalId.
        try {
            $managerRow = $this->connection->fetchAssociative(
                'SELECT id FROM manager WHERE external_id = :external_id',
                ['external_id' => $contact->managerExternalId()]
            );
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to resolve manager for contact.', $exception);
        }

        if ($managerRow === false) {
            throw new BadRequestException(
                sprintf('Manager with externalId "%s" not found.', $contact->managerExternalId())
            );
        }

        $managerId = (int) $managerRow['id'];

        try {
            $this->connection->executeStatement(
                'INSERT INTO contact (external_id, firstname, lastname, email, phone, note, manager_id, created_at, updated_at) 
                 VALUES (:external_id, :firstname, :lastname, :email, :phone, :note, :manager_id, :created_at, :updated_at)',
                [
                    'external_id' => $externalId->toString(),
                    'firstname'   => $contact->firstname(),
                    'lastname'    => $contact->lastname(),
                    'email'       => $contact->email(),
                    'phone'       => $contact->phone(),
                    'note'        => $contact->note(),
                    'manager_id'  => $managerId,
                    'created_at'  => $now->format('Y-m-d H:i:s'),
                    'updated_at'  => $now->format('Y-m-d H:i:s'),
                ]
            );
        } catch (UniqueConstraintViolationException $exception) {
            throw new BadRequestException('Contact with given email already exists.', $exception);
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to create contact.', $exception);
        }

        return $externalId;
    }

    /**
     * @inheritDoc
     */
    public function find(ExternalId $externalId): Contact
    {
        try {
            $qb = $this->connection->createQueryBuilder()
                ->select(
                    'c.external_id',
                    'c.firstname',
                    'c.lastname',
                    'c.email',
                    'c.phone',
                    'c.note',
                    'm.external_id AS manager_external_id',
                    'm.firstname   AS manager_firstname',
                    'm.lastname    AS manager_lastname'
                )
                ->from('contact', 'c')
                ->leftJoin('c', 'manager', 'm', 'c.manager_id = m.id')
                ->where('c.external_id = :external_id')
                ->setParameter('external_id', $externalId->toString());

            $row = $qb->executeQuery()->fetchAssociative();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to load contact.', $exception);
        }

        if ($row === false) {
            throw new NotFoundException(sprintf('Contact with externalId "%s" not found.', $externalId->toString()));
        }

        if (isset($row['manager_external_id']) && $row['manager_external_id'] !== null) {
            $manager = Manager::create(
                ExternalId::fromString((string) $row['manager_external_id']),
                (string) $row['manager_firstname'],
                (string) $row['manager_lastname']
            );
        }

        return Contact::create(
            ExternalId::fromString((string) $row['external_id']),
            (string) $row['firstname'],
            (string) $row['lastname'],
            (string) $row['email'],
            $row['phone'] !== null ? (string) $row['phone'] : null,
            isset($row['note']) ? (string) $row['note'] : null,
            $manager ?? null
        );
    }

    /**
     * @inheritDoc
     */
    public function search(SearchFilter $filter, Pagination $pagination): ContactList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.external_id', 'c.firstname', 'c.lastname', 'c.email', 'c.phone')
            ->from('contact', 'c');

        $this->applySearchFilter($qb, $filter);

        $qb
            ->setFirstResult($pagination->offset())
            ->setMaxResults($pagination->perPage());

        try {
            $rows = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to search contacts.', $exception);
        }

        $contacts = [];

        foreach ($rows as $row) {
            $contacts[] = ContactSummary::create(
                ExternalId::fromString((string) $row['external_id']),
                (string) $row['firstname'],
                (string) $row['lastname'],
                (string) $row['email'],
                $row['phone'] !== null ? (string) $row['phone'] : null
            );
        }

        return new ContactList(...$contacts);
    }

    /**
     * @inheritDoc
     */
    public function findByManager(ExternalId $managerExternalId): ContactList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.external_id', 'c.firstname', 'c.lastname', 'c.email', 'c.phone')
            ->from('contact', 'c')
            ->innerJoin('c', 'manager', 'm', 'c.manager_id = m.id')
            ->where('m.external_id = :manager_external_id')
            ->setParameter('manager_external_id', $managerExternalId->toString());

        try {
            $rows = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to load contacts for manager.', $exception);
        }

        $contacts = [];

        foreach ($rows as $row) {
            $contacts[] = ContactSummary::create(
                ExternalId::fromString((string) $row['external_id']),
                (string) $row['firstname'],
                (string) $row['lastname'],
                (string) $row['email'],
                $row['phone'] !== null ? (string) $row['phone'] : null
            );
        }

        return new ContactList(...$contacts);
    }

    /**
     * @inheritDoc
     */
    public function findManagerForContact(ExternalId $externalId): Manager
    {
        try {
            $row = $this->connection->fetchAssociative(
                'SELECT m.external_id, m.firstname, m.lastname
                 FROM contact c
                 INNER JOIN manager m ON c.manager_id = m.id
                 WHERE c.external_id = :external_id',
                ['external_id' => $externalId->toString()]
            );
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to load manager for contact.', $exception);
        }

        if ($row === false) {
            throw new NotFoundException(sprintf('Manager for contact "%s" not found.', $externalId->toString()));
        }

        return Manager::create(
            ExternalId::fromString((string) $row['external_id']),
            (string) $row['firstname'],
            (string) $row['lastname']
        );
    }

    private function applySearchFilter(QueryBuilder $qb, SearchFilter $filter): void
    {
        if (null !== $filter->firstname() && $filter->firstname() !== '') {
            $qb
                ->andWhere('c.firstname ILIKE :firstname')
                ->setParameter('firstname', '%' . $filter->firstname() . '%');
        }

        if (null !== $filter->lastname() && $filter->lastname() !== '') {
            $qb
                ->andWhere('c.lastname ILIKE :lastname')
                ->setParameter('lastname', '%' . $filter->lastname() . '%');
        }

        if (null !== $filter->email() && $filter->email() !== '') {
            $qb
                ->andWhere('c.email = :email')
                ->setParameter('email', $filter->email());
        }

        if (null !== $filter->phone() && $filter->phone() !== '') {
            $qb
                ->andWhere('c.phone = :phone')
                ->setParameter('phone', $filter->phone());
        }
    }
}
