<?php

declare(strict_types=1);

namespace App\Services\Repository\Dbal;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\CreateContact;
use App\Domain\Contact\Contact;
use App\Domain\Contact\ContactList;
use App\Domain\Contact\Exception\ContactNotFound;
use App\Domain\Contact\SearchFilter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final class DBALContactRepository implements ContactRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create(CreateContact $contact): string
    {
        $externalId = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();

        $this->connection->executeStatement(
            'INSERT INTO contact (external_id, firstname, lastname, email, phone, created_at, updated_at) 
             VALUES (:external_id, :firstname, :lastname, :email, :phone, :created_at, :updated_at)',
            [
                'external_id' => $externalId,
                'firstname' => $contact->firstname(),
                'lastname' => $contact->lastname(),
                'email' => $contact->email(),
                'phone' => $contact->phone(),
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]
        );

        return $externalId;
    }

    public function find(string $externalId): Contact
    {
        $row = $this->connection->fetchAssociative(
            'SELECT external_id, firstname, lastname, email, phone FROM contact WHERE external_id = :external_id',
            ['external_id' => $externalId]
        );

        if ($row === false) {
            throw ContactNotFound::forExternalId($externalId);
        }

        return Contact::create(
            (string) $row['external_id'],
            (string) $row['firstname'],
            (string) $row['lastname'],
            (string) $row['email'],
            $row['phone'] !== null ? (string) $row['phone'] : null
        );
    }

    public function search(SearchFilter $filter): ContactList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.external_id', 'c.firstname', 'c.lastname', 'c.email', 'c.phone')
            ->from('contact', 'c');

        $this->applySearchFilter($qb, $filter);

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $contacts = [];

        foreach ($rows as $row) {
            $contacts[] = Contact::create(
                (string) $row['external_id'],
                (string) $row['firstname'],
                (string) $row['lastname'],
                (string) $row['email'],
                $row['phone'] !== null ? (string) $row['phone'] : null
            );
        }

        return new ContactList(...$contacts);
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


