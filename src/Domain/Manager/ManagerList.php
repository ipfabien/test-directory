<?php

declare(strict_types=1);

namespace App\Domain\Manager;

use App\Shared\Normalization\Normalizable;

/**
 * Value object representing a collection of manager summaries.
 *
 * @implements \IteratorAggregate<int, ManagerSummary>
 */
final class ManagerList implements \IteratorAggregate, \Countable, Normalizable
{
    /**
     * @var ManagerSummary[]
     */
    private array $managers;

    public function __construct(ManagerSummary ...$managers)
    {
        $this->managers = $managers;
    }

    /**
     * @return \Traversable<int, ManagerSummary>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->managers);
    }

    public function count(): int
    {
        return \count($this->managers);
    }

    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): self
    {
        $managers = [];

        foreach ($data as $item) {
            if (!\is_array($item)) {
                continue;
            }

            $managers[] = ManagerSummary::denormalize($item);
        }

        return new self(...$managers);
    }

    /**
     * @return array<mixed>
     */
    public function normalize(): array
    {
        return array_map(
            static function (ManagerSummary $manager): array {
                return $manager->normalize();
            },
            $this->managers
        );
    }
}
