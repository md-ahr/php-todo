<?php

declare(strict_types=1);

namespace App\Todos;

/**
 * Parsed list/filter state from GET (?q=&status=&priority=&page=&per_page=)
 * plus round-trip helpers for POST (hidden fields prefixed with state_).
 */
final class TodoListState
{
    private const ALLOWED_PER_PAGE = [5, 8, 12];

    public function __construct(
        public readonly string $q,
        public readonly string $status,
        public readonly string $priority,
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    public static function fromGlobals(): self
    {
        $qRaw = trim((string) ($_GET['q'] ?? ''));

        $status = strtolower((string) ($_GET['status'] ?? 'all'));
        if (!in_array($status, ['all', 'active', 'done'], true)) {
            $status = 'all';
        }

        $priority = strtolower((string) ($_GET['priority'] ?? 'all'));
        if (!in_array($priority, ['all', 'high', 'med', 'low'], true)) {
            $priority = 'all';
        }

        $perPage = (int) ($_GET['per_page'] ?? 8);
        if (!in_array($perPage, self::ALLOWED_PER_PAGE, true)) {
            $perPage = 8;
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));

        return new self(mb_substr($qRaw, 0, 200), $status, $priority, $page, $perPage);
    }

    /**
     * Hidden fields prefixed e.g. `state_q`, `state_status`, … — from POST persistence.
     */
    public static function fromPostPrefixes(array $post, string $prefix = 'state_'): self
    {
        $qRaw = trim((string) ($post[$prefix . 'q'] ?? ''));
        $status = strtolower((string) ($post[$prefix . 'status'] ?? 'all'));
        if (!in_array($status, ['all', 'active', 'done'], true)) {
            $status = 'all';
        }
        $priority = strtolower((string) ($post[$prefix . 'priority'] ?? 'all'));
        if (!in_array($priority, ['all', 'high', 'med', 'low'], true)) {
            $priority = 'all';
        }
        $perPage = (int) ($post[$prefix . 'per_page'] ?? 8);
        if (!in_array($perPage, self::ALLOWED_PER_PAGE, true)) {
            $perPage = 8;
        }
        $page = max(1, (int) ($post[$prefix . 'page'] ?? 1));

        return new self(mb_substr($qRaw, 0, 200), $status, $priority, $page, $perPage);
    }

    public function withPage(int $page): self
    {
        return new self($this->q, $this->status, $this->priority, max(1, $page), $this->perPage);
    }

    /** Full bookmarkable GET string including defaults. */
    public function fullQuery(): string
    {
        return http_build_query([
            'q' => $this->q,
            'status' => $this->status,
            'priority' => $this->priority,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ]);
    }

    public function normalizedForTotal(int $totalRows, int $perPageUsed): self
    {
        $lastPage = max(1, (int) ceil($totalRows / $perPageUsed));
        $page = min(max(1, $this->page), $lastPage);

        return new self($this->q, $this->status, $this->priority, $page, $this->perPage);
    }

    /** @return list<array{name:string,value:string}> */
    public function hiddenInputs(string $namePrefix = 'state_'): array
    {
        return [
            ['name' => $namePrefix . 'q', 'value' => $this->q],
            ['name' => $namePrefix . 'status', 'value' => $this->status],
            ['name' => $namePrefix . 'priority', 'value' => $this->priority],
            ['name' => $namePrefix . 'page', 'value' => (string) $this->page],
            ['name' => $namePrefix . 'per_page', 'value' => (string) $this->perPage],
        ];
    }
}
