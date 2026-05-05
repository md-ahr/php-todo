<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Auth\RequireAuthentication;
use App\Repositories\TodoRepository;
use App\Todos\TodoListState;
use App\Validation\TodoValidator;
use PDOException;

final class TodosController
{
    public function index(): void
    {
        RequireAuthentication::redirectToLoginIfGuest('/todos');

        $userId = (int) auth_user()['user_id'];

        $welcomeFlash = isset($_SESSION['_flash_welcome']) && is_string($_SESSION['_flash_welcome'])
            ? $_SESSION['_flash_welcome']
            : '';
        unset($_SESSION['_flash_welcome']);

        $todoNotice = $_SESSION['_todo_notice'] ?? null;
        unset($_SESSION['_todo_notice']);

        try {
            $repo = new TodoRepository(db());

            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                $this->handleTodoPost($userId, $repo);
            }

            $this->renderList($userId, $repo, $welcomeFlash, $todoNotice);
        } catch (PDOException $e) {
            $this->renderListError($e, $welcomeFlash, $todoNotice);
        }
    }

    private function todoRedirect303(TodoListState $returnState): never
    {
        header('Location: /todos?' . $returnState->fullQuery(), true, 303);
        exit;
    }

    private function handleTodoPost(int $userId, TodoRepository $repo): never
    {
        $returnState = TodoListState::fromPostPrefixes($_POST);

        if (!csrf_validate($_POST['_token'] ?? null)) {
            $_SESSION['_todo_notice'] = ['type' => 'error', 'text' => 'Session expired — refresh and try again.'];
            $this->todoRedirect303($returnState);
        }

        $action = strtolower(trim((string) ($_POST['_action'] ?? '')));

        switch ($action) {
            case 'create':
                $this->todoCreatePost($userId, $repo, $returnState);

            case 'update':
                $this->todoUpdatePost($userId, $repo, $returnState);

            case 'delete':
                $this->todoDeletePost($userId, $repo, $returnState);

            case 'toggle':
                $this->todoTogglePost($userId, $repo, $returnState);

            default:
                $_SESSION['_todo_notice'] = ['type' => 'error', 'text' => 'Unsupported action.'];
                $this->todoRedirect303($returnState);
        }
    }

    private function todoCreatePost(int $userId, TodoRepository $repo, TodoListState $returnState): never
    {
        $errors = TodoValidator::validateCreate($_POST);
        if ($errors !== []) {
            $_SESSION['_todo_notice'] = [
                'type' => 'error',
                'text' => implode(' ', array_values($errors)),
            ];
            $this->todoRedirect303($returnState);
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $priority = strtolower(trim((string) ($_POST['priority'] ?? 'med')));

        try {
            $repo->create($userId, $title, $notes, $priority);
        } catch (PDOException $e) {
            $this->pdoFailureToNotice($e, $returnState, 'Could not save the todo.');
        }

        $_SESSION['_todo_notice'] = ['type' => 'success', 'text' => 'Todo added.'];
        $this->todoRedirect303($returnState);
    }

    private function todoUpdatePost(int $userId, TodoRepository $repo, TodoListState $returnState): never
    {
        $errors = TodoValidator::validateUpdate($_POST);
        if ($errors !== []) {
            $_SESSION['_todo_notice'] = [
                'type' => 'error',
                'text' => implode(' ', array_values($errors)),
            ];
            $this->todoRedirect303($returnState);
        }

        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $priority = strtolower(trim((string) ($_POST['priority'] ?? 'med')));

        try {
            $ok = $repo->update($userId, $id, $title, $notes, $priority);
        } catch (PDOException $e) {
            $this->pdoFailureToNotice($e, $returnState, 'Could not update the todo.');
        }

        $_SESSION['_todo_notice'] = $ok
            ? ['type' => 'success', 'text' => 'Todo updated.']
            : ['type' => 'error', 'text' => 'Could not update that todo.'];

        $this->todoRedirect303($returnState);
    }

    private function todoDeletePost(int $userId, TodoRepository $repo, TodoListState $returnState): never
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['_todo_notice'] = ['type' => 'error', 'text' => 'Invalid todo.'];
            $this->todoRedirect303($returnState);
        }

        try {
            $ok = $repo->delete($userId, $id);
        } catch (PDOException $e) {
            $this->pdoFailureToNotice($e, $returnState, 'Could not delete the todo.');
        }

        $_SESSION['_todo_notice'] = $ok
            ? ['type' => 'success', 'text' => 'Todo deleted.']
            : ['type' => 'error', 'text' => 'Could not delete that todo.'];

        $this->todoRedirect303($returnState);
    }

    private function todoTogglePost(int $userId, TodoRepository $repo, TodoListState $returnState): never
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['_todo_notice'] = ['type' => 'error', 'text' => 'Invalid todo.'];
            $this->todoRedirect303($returnState);
        }

        try {
            $ok = $repo->toggleComplete($userId, $id);
        } catch (PDOException $e) {
            $this->pdoFailureToNotice($e, $returnState, 'Could not update status.');
        }

        $_SESSION['_todo_notice'] = $ok
            ? ['type' => 'success', 'text' => 'Status updated.']
            : ['type' => 'error', 'text' => 'Could not toggle that todo.'];

        $this->todoRedirect303($returnState);
    }

    private function pdoFailureToNotice(PDOException $e, TodoListState $returnState, string $fallbackMsg): never
    {
        $errno = $e->errorInfo[1] ?? null;
        $missing = ($errno === 1146) || str_contains($e->getMessage(), 'Base table or view not found');

        $_SESSION['_todo_notice'] = $missing
            ? ['type' => 'error', 'text' => 'Todos table missing. Run: php database/migrate.php']
            : (((bool) ($GLOBALS['config']['debug'] ?? false))
                ? ['type' => 'error', 'text' => $e->getMessage()]
                : ['type' => 'error', 'text' => $fallbackMsg]);

        $this->todoRedirect303($returnState);
    }

    private function renderList(int $userId, TodoRepository $repo, string $welcomeFlash, mixed $todoNotice): void
    {
        $migrateHint = '';

        $state = TodoListState::fromGlobals();
        $totalFiltered = $repo->countFiltered($userId, $state);
        $stateAligned = $state->normalizedForTotal($totalFiltered, $state->perPage);

        if ($stateAligned->fullQuery() !== $state->fullQuery()) {
            header('Location: /todos?' . $stateAligned->fullQuery(), true, 303);
            exit;
        }

        $counts = $repo->aggregateCounts($userId);
        /** @var list<array{id:int,user_id:int,title:string,notes:?string,priority:string,is_completed:int,created_at:string,updated_at:string}> $items */
        $items = $repo->paginate($userId, $stateAligned);

        $lastPage = max(1, (int) ceil($totalFiltered / max(1, $stateAligned->perPage)));

        $from = $totalFiltered === 0 ? 0 : (($stateAligned->page - 1) * $stateAligned->perPage) + 1;
        $to = min($stateAligned->page * $stateAligned->perPage, $totalFiltered);

        $dbConnected = true;
        $dbTitle = '';
        try {
            /** @var string|false $version */
            $version = db()->query('SELECT VERSION()')->fetchColumn();
            $dbTitle = is_string($version) ? $version : 'MySQL (PDO)';
        } catch (PDOException $e) {
            $debug = (bool) ($GLOBALS['config']['debug'] ?? false);
            $dbConnected = false;
            $dbTitle = $debug ? $e->getMessage() : 'Offline';
        }

        require view_path('todos.view.php');
    }

    private function renderListError(PDOException $e, string $welcomeFlash, mixed $todoNotice): void
    {
        $errno = $e->errorInfo[1] ?? null;
        $missing = ($errno === 1146) || str_contains($e->getMessage(), 'Base table or view not found');

        $migrateHint = $missing
            ? 'Todos table missing. From the project root run: php database/migrate.php'
            : '';

        $counts = ['total' => 0, 'active' => 0, 'done' => 0];

        $items = [];
        $stateAligned = TodoListState::fromGlobals()->normalizedForTotal(0, 8);

        $totalFiltered = 0;
        $lastPage = 1;
        $from = 0;
        $to = 0;

        if (!is_array($todoNotice)) {
            $todoNotice = [
                'type' => 'error',
                'text' => $missing
                    ? 'Apply the database migration, then reload this page.'
                    : (((bool) ($GLOBALS['config']['debug'] ?? false))
                        ? $e->getMessage()
                        : 'Could not reach the database.'),
            ];
        }

        $dbConnected = false;
        $dbTitle = (bool) ($GLOBALS['config']['debug'] ?? false) ? $e->getMessage() : 'Offline';

        require view_path('todos.view.php');
    }
}
