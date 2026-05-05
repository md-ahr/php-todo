<?php

declare(strict_types=1);

namespace App\Validation;

final class TodoValidator
{
    private const TITLE_MAX = 500;
    private const NOTES_MAX = 20000;

    /** @return array<string, string> */
    public static function validateCreate(array $input): array
    {
        return self::validateBody($input);
    }

    /** @return array<string, string> */
    public static function validateUpdate(array $input): array
    {
        $errors = self::validateBody($input);
        $id = (int) ($input['id'] ?? 0);
        if ($id <= 0) {
            $errors['id'] = 'Missing or invalid todo.';
        }

        return $errors;
    }

    /** @return array<string, string> */
    private static function validateBody(array $input): array
    {
        $errors = [];

        $title = trim((string) ($input['title'] ?? ''));
        $notes = trim((string) ($input['notes'] ?? ''));
        $priority = strtolower(trim((string) ($input['priority'] ?? 'med')));

        if ($title === '') {
            $errors['title'] = 'Please enter a title.';
        } elseif (mb_strlen($title) > self::TITLE_MAX) {
            $errors['title'] = 'Title is too long (max ' . self::TITLE_MAX . ' characters).';
        }

        if (mb_strlen($notes) > self::NOTES_MAX) {
            $errors['notes'] = 'Notes are too long.';
        }

        if (!in_array($priority, ['low', 'med', 'high'], true)) {
            $errors['priority'] = 'Invalid priority.';
        }

        return $errors;
    }
}
