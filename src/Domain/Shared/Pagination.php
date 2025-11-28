<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Pagination
{
    private int $page;

    private int $perPage;

    private function __construct(int $page, int $perPage)
    {
        $this->page    = $page;
        $this->perPage = $perPage;
    }

    public static function create(?int $page, ?int $perPage): self
    {
        $page    = $page    ?? 1;
        $perPage = $perPage ?? 20;

        if ($page < 1) {
            $page = 1;
        }

        if ($perPage < 1) {
            $perPage = 1;
        }

        if ($perPage > 100) {
            $perPage = 100;
        }

        return new self($page, $perPage);
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
