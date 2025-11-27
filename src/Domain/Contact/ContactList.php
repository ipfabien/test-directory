<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Shared\Normalization\Normalizable;

/**
 * Value object representing a collection of contacts.
 *
 * @implements \IteratorAggregate<int, Contact>
 */
final class ContactList implements \IteratorAggregate, \Countable, Normalizable
{
    /**
     * @var Contact[]
     */
    private array $contacts;

    public function __construct(Contact ...$contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * @return \Traversable<int, Contact>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->contacts);
    }

    public function count(): int
    {
        return \count($this->contacts);
    }

    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): self
    {
        $contacts = [];

        foreach ($data as $item) {
            if (!\is_array($item)) {
                continue;
            }

            $contacts[] = Contact::denormalize($item);
        }

        return new self(...$contacts);
    }

    /**
     * @return array<mixed>
     */
    public function normalize(): array
    {
        return array_map(
            static function (Contact $contact): array {
                return $contact->normalize();
            },
            $this->contacts
        );
    }
}


