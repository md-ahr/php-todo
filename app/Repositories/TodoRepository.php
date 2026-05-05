<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Todos\TodoListState;
use PDO;

final class TodoRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return array{total:int,active:int,done:int} */
    public function aggregateCounts(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN is_completed = 0 THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS done
             FROM todos WHERE user_id = ?'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'active' => (int) ($row['active'] ?? 0),
            'done' => (int) ($row['done'] ?? 0),
        ];
    }

    /**
     * @return array{0: string, 1: array<int, mixed>}
     */
    private function buildWhere(TodoListState $state, int $userId): array
    {
        $parts = ['t.user_id = ?'];
        $params = [$userId];

        if ($state->priority !== 'all') {
            $parts[] = 't.priority = ?';
            $params[] = $state->priority;
        }

        if ($state->status === 'active') {
            $parts[] = 't.is_completed = 0';
        } elseif ($state->status === 'done') {
            $parts[] = 't.is_completed = 1';
        }

        if ($state->q !== '') {
            $needle = '%' . self::escapeLikeBang($state->q) . '%';
            $parts[] = '(t.title LIKE ? ESCAPE \'!\' OR COALESCE(t.notes, \'\') LIKE ? ESCAPE \'!\')';
            $params[] = $needle;
            $params[] = $needle;
        }

        return ['WHERE ' . implode(' AND ', $parts), $params];
    }

    private static function escapeLikeBang(string $value): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
    }

    public function countFiltered(int $userId, TodoListState $state): int
    {
        [$where, $params] = $this->buildWhere($state, $userId);
        $sql = 'SELECT COUNT(*) FROM todos t ' . $where;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return list<array{id:int,user_id:int,title:string,notes:?string,priority:string,is_completed:int,created_at:string,updated_at:string}>
     */
    public function paginate(int $userId, TodoListState $state): array
    {
        [$where, $params] = $this->buildWhere($state, $userId);

        $order = ' ORDER BY t.is_completed ASC,
            FIELD(t.priority, ' . "'high','med','low'" . '),
            t.created_at DESC';

        $limit = max(1, $state->perPage);
        $offset = max(0, ($state->page - 1) * $limit);

        $sql = 'SELECT t.id, t.user_id, t.title, t.notes, t.priority, t.is_completed, t.created_at, t.updated_at
                FROM todos t ' . $where . $order
            . ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => (int) $r['id'],
                'user_id' => (int) $r['user_id'],
                'title' => (string) $r['title'],
                'notes' => $r['notes'] === null ? null : (string) $r['notes'],
                'priority' => (string) $r['priority'],
                'is_completed' => (int) $r['is_completed'],
                'created_at' => (string) $r['created_at'],
                'updated_at' => (string) $r['updated_at'],
            ];
        }

        return $out;
    }

    public function create(int $userId, string $title, string $notes, string $priority): int
    {
        $notesParam = $notes === '' ? null : $notes;

        $stmt = $this->pdo->prepare(
            'INSERT INTO todos (user_id, title, notes, priority, is_completed, created_at, updated_at)
             VALUES (?, ?, ?, ?, 0, NOW(), NOW())'
        );
        $stmt->execute([$userId, $title, $notesParam, $priority]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $userId, int $id, string $title, string $notes, string $priority): bool
    {
        $notesParam = $notes === '' ? null : $notes;

        $stmt = $this->pdo->prepare(
            'UPDATE todos SET title = ?, notes = ?, priority = ?, updated_at = NOW()
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$title, $notesParam, $priority, $id, $userId]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $userId, int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }

    public function toggleComplete(int $userId, int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE todos SET is_completed = CASE WHEN is_completed = 1 THEN 0 ELSE 1 END,
                updated_at = NOW()
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }
}
