<?php
$welcomeFlash ??= '';
$migrateHint ??= '';
$todoNotice ??= null;

$counts ??= ['total' => 0, 'active' => 0, 'done' => 0];
/** @var array<int, mixed> $items */
$items ??= [];
$dbConnected ??= false;
$dbTitle ??= '';
$totalFiltered ??= 0;
$lastPage ??= 1;
$from ??= 0;
$to ??= 0;

$lim = \App\Subscriptions\TodoQuota::freeTodoLimit();
$quota ??= [
    'limit' => $lim,
    'total' => (int) ($counts['total'] ?? 0),
    'subscribed' => false,
    'can_create' => (int) ($counts['total'] ?? 0) < $lim,
    'remaining' => max(0, $lim - (int) ($counts['total'] ?? 0)),
];

/** @var \App\Todos\TodoListState $stateAligned */
$stateAligned ??= \App\Todos\TodoListState::fromGlobals();

if (!function_exists('e')) {
    function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}
?>

<?php include_once __DIR__ . '/components/head.php' ?>

<main class="relative min-h-[calc(100vh-81px)]">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -right-40 -top-40 h-80 w-80 rounded-full bg-indigo-500/15 blur-3xl"
             aria-hidden="true"></div>
        <div class="absolute -left-40 bottom-0 h-72 w-72 rounded-full bg-violet-600/10 blur-3xl"
             aria-hidden="true"></div>
    </div>

    <div class="mx-auto max-w-5xl px-4 pb-16 pt-10 sm:px-6 lg:px-8 lg:pt-14">
        <?php if (($quota['subscribed'] ?? false) === false): ?>
            <div class="mb-6 rounded-xl border <?= !($quota['can_create'] ?? true) ? 'border-amber-500/40 bg-amber-500/10' : 'border-white/10 bg-white/5' ?> px-4 py-3 text-sm text-gray-200"
                 role="status">
                <?php if (($quota['can_create'] ?? true) === false): ?>
                    <p class="font-semibold text-amber-50">You have reached the <?= (int) $quota['limit'] ?>-todo free limit.</p>
                    <p class="mt-2 text-amber-100/90">
                        <a href="/subscribe" class="font-semibold text-indigo-300 underline decoration-indigo-400/50 underline-offset-4 hover:text-indigo-200">
                            Subscribe</a> for unlimited todos. You can still edit or remove existing tasks.
                    </p>
                <?php else: ?>
                    <p>
                        Free tier: <span class="font-semibold text-white"><?= (int) $quota['total'] ?></span> /
                        <span class="font-semibold text-white"><?= (int) $quota['limit'] ?></span> todos.
                        <?php if (($quota['remaining'] ?? 0) > 0): ?>
                            <span class="text-gray-400">· <?= (int) $quota['remaining'] ?> <?= (int) $quota['remaining'] === 1 ? 'slot' : 'slots' ?> left before you need a subscription.</span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($welcomeFlash !== ''): ?>
            <div class="mb-6 rounded-xl border border-emerald-500/35 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100"
                 role="status">
                <p class="font-medium text-emerald-50"><?= e($welcomeFlash) ?></p>
            </div>
        <?php endif; ?>

        <?php if (is_array($todoNotice) && isset($todoNotice['text'], $todoNotice['type'])): ?>
            <?php
            $isErr = ($todoNotice['type'] ?? '') === 'error';
            ?>
            <div class="mb-6 rounded-xl px-4 py-3 text-sm <?= $isErr ? 'border border-red-500/35 bg-red-500/10 text-red-100' : 'border border-emerald-500/35 bg-emerald-500/10 text-emerald-100' ?>"
                 role="status">
                <p class="font-medium <?= $isErr ? 'text-white' : 'text-emerald-50' ?>"><?= e((string) $todoNotice['text']) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($migrateHint !== ''): ?>
            <div class="mb-6 rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100"
                 role="alert">
                <p class="font-medium"><?= e($migrateHint) ?></p>
            </div>
        <?php endif; ?>

        <header class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-400">Workspace</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">My todos</h1>
                <p class="mt-2 max-w-xl text-sm text-gray-400 sm:text-base">
                    Create, search, filter, and complete tasks.
                </p>
                <dl class="mt-6 flex flex-wrap gap-4 text-sm" aria-live="polite">
                    <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                        <dt class="text-xs text-gray-500">Total</dt>
                        <dd class="font-semibold text-white"><?= (int) $counts['total'] ?></dd>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                        <dt class="text-xs text-gray-500">Active</dt>
                        <dd class="font-semibold text-amber-200"><?= (int) $counts['active'] ?></dd>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                        <dt class="text-xs text-gray-500">Done</dt>
                        <dd class="font-semibold text-emerald-300"><?= (int) $counts['done'] ?></dd>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                        <dt class="text-xs text-gray-500">Database</dt>
                        <dd class="font-semibold <?= !empty($dbConnected) ? 'text-emerald-300' : 'text-rose-300' ?>"
                            title="<?= e($dbTitle) ?>">
                            <?= !empty($dbConnected) ? 'Connected' : 'Offline' ?>
                        </dd>
                    </div>
                </dl>
            </div>
            <?php if (!empty($quota['can_create'])): ?>
            <button type="button" id="btn-open-add"
                    class="shrink-0 rounded-lg bg-indigo-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                Add todo
            </button>
            <?php else: ?>
            <a href="/subscribe"
               class="inline-flex shrink-0 items-center justify-center rounded-lg bg-indigo-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                Subscribe to add todos
            </a>
            <?php endif; ?>
        </header>

        <section
                class="mt-10 rounded-2xl border border-white/10 bg-gray-950/50 p-4 shadow-xl ring-1 ring-white/5 backdrop-blur sm:p-6"
                aria-labelledby="todos-controls-heading">
            <h2 id="todos-controls-heading" class="sr-only">Search and filter todos</h2>

            <form id="filters-form" method="get" action="/todos" class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <input type="hidden" name="page" value="1"/>
                <div class="flex min-w-0 flex-1 flex-col gap-2">
                    <label for="todo-search" class="text-xs font-medium text-gray-400">Search</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                        <input id="todo-search" name="q" type="search" placeholder="Search titles and notes…"
                               value="<?= e($stateAligned->q) ?>" autocomplete="off"
                               class="w-full rounded-xl border border-white/10 bg-gray-900/80 py-2.5 pl-10 pr-4 text-sm text-white placeholder:text-gray-500 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"/>
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-4">
                    <div class="flex flex-col gap-2 sm:min-w-[10rem]">
                        <label for="filter-status" class="text-xs font-medium text-gray-400">Status</label>
                        <div class="relative">
                            <select id="filter-status" name="status"
                                    class="filter-auto w-full cursor-pointer appearance-none rounded-xl border border-white/10 bg-gray-900/80 py-2.5 pl-3.5 pr-10 text-sm text-white shadow-sm focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <option value="all" <?= $stateAligned->status === 'all' ? 'selected' : '' ?>>All tasks</option>
                                <option value="active" <?= $stateAligned->status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="done" <?= $stateAligned->status === 'done' ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                                  aria-hidden="true">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:min-w-[10rem]">
                        <label for="filter-priority" class="text-xs font-medium text-gray-400">Priority</label>
                        <div class="relative">
                            <select id="filter-priority" name="priority"
                                    class="filter-auto w-full cursor-pointer appearance-none rounded-xl border border-white/10 bg-gray-900/80 py-2.5 pl-3.5 pr-10 text-sm text-white shadow-sm focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <option value="all" <?= $stateAligned->priority === 'all' ? 'selected' : '' ?>>Any priority</option>
                                <option value="high" <?= $stateAligned->priority === 'high' ? 'selected' : '' ?>>High</option>
                                <option value="med" <?= $stateAligned->priority === 'med' ? 'selected' : '' ?>>Medium</option>
                                <option value="low" <?= $stateAligned->priority === 'low' ? 'selected' : '' ?>>Low</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                                  aria-hidden="true">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:min-w-[7rem]">
                        <label for="page-size" class="text-xs font-medium text-gray-400">Per page</label>
                        <div class="relative">
                            <select id="page-size" name="per_page"
                                    class="filter-auto w-full cursor-pointer appearance-none rounded-xl border border-white/10 bg-gray-900/80 py-2.5 pl-3.5 pr-10 text-sm text-white shadow-sm focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <?php foreach ([5, 8, 12] as $opt): ?>
                                    <option value="<?= $opt ?>" <?= $stateAligned->perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                                  aria-hidden="true">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-end gap-2">
                        <button type="submit"
                                class="rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15">
                            Apply filters
                        </button>
                        <a href="/todos"
                           class="rounded-xl px-4 py-2.5 text-center text-sm font-semibold text-gray-300 ring-1 ring-white/15 transition hover:bg-white/5 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                            Reset filters
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <section class="mt-8" aria-labelledby="todos-list-heading">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 id="todos-list-heading" class="text-lg font-semibold text-white">Tasks</h2>
                <p class="text-xs text-gray-500 sm:text-sm">
                    Showing <?= (int) $from ?>–<?= (int) $to ?> of <?= (int) $totalFiltered ?>
                </p>
            </div>

            <?php if ($totalFiltered === 0): ?>
                <div class="mt-8 rounded-2xl border border-dashed border-white/15 bg-gray-950/40 px-6 py-14 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                    </svg>
                    <p class="mt-4 text-sm font-medium text-gray-300">Nothing matches</p>
                    <p class="mt-2 text-sm text-gray-500">
                        Adjust filters or add your first task.
                    </p>
                </div>
            <?php else: ?>
                <ul class="mt-4 flex flex-col gap-3" role="list" aria-label="Todo list">
                    <?php
                    $prioStyles = [
                            'high' => 'bg-rose-500/15 text-rose-200 ring-rose-400/30',
                            'med' => 'bg-amber-500/15 text-amber-200 ring-amber-400/25',
                            'low' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/25',
                    ];
                    $prioLabel = ['high' => 'High', 'med' => 'Medium', 'low' => 'Low'];
                    ?>
                    <?php foreach ($items as $todo): ?>
                        <?php
                        $done = !empty($todo['is_completed']);
                        $p = $todo['priority'];
                        $prioCls = $prioStyles[$p] ?? $prioStyles['med'];
                        $editPayload = [
                                'id' => $todo['id'],
                                'title' => $todo['title'],
                                'notes' => (string) ($todo['notes'] ?? ''),
                                'priority' => $todo['priority'],
                        ];
                        $editJson = json_encode($editPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
                        ?>
                        <li class="rounded-2xl border border-white/10 bg-gray-950/60 p-4 ring-1 ring-white/5 backdrop-blur">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0 flex flex-1 gap-3">
                                    <form method="post" action="/todos" class="flex shrink-0 items-start pt-1">
                                        <?php foreach ($stateAligned->hiddenInputs() as $h): ?>
                                            <input type="hidden" name="<?= e($h['name']) ?>" value="<?= e($h['value']) ?>"/>
                                        <?php endforeach; ?>
                                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"/>
                                        <input type="hidden" name="_action" value="toggle"/>
                                        <input type="hidden" name="id" value="<?= (int) $todo['id'] ?>"/>
                                        <button type="submit"
                                                class="flex h-10 w-10 items-center justify-center rounded-lg border <?= $done ? 'border-emerald-500/50 bg-emerald-500/15 text-emerald-200' : 'border-white/15 bg-white/5 text-gray-400' ?> transition hover:border-indigo-400/50"
                                                title="<?= $done ? 'Mark active' : 'Mark done' ?>"
                                                aria-label="<?= $done ? 'Mark this task as active' : 'Mark this task as completed' ?>">
                                            <?php if ($done): ?>
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                     stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                                </svg>
                                            <?php else: ?>
                                                <span class="block h-5 w-5 rounded border border-white/20" aria-hidden="true"></span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-base font-semibold leading-snug <?= $done ? 'text-gray-500 line-through' : 'text-white' ?>">
                                                <?= e($todo['title']) ?>
                                            </h3>
                                            <span class="inline-flex shrink-0 rounded-md px-2 py-0.5 text-xs font-medium ring-1 <?= e($prioCls) ?>">
                                                <?= e($prioLabel[$p] ?? $p) ?>
                                            </span>
                                        </div>
                                        <?php if (($todo['notes'] ?? '') !== '' && $todo['notes'] !== null): ?>
                                            <p class="whitespace-pre-wrap text-sm text-gray-400"><?= e((string) $todo['notes']) ?></p>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-600">
                                            Updated <?= e(date('M j, Y · g:i A', strtotime($todo['updated_at']))) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex shrink-0 flex-wrap items-center gap-2 sm:flex-col sm:items-end">
                                    <button type="button"
                                            class="todo-edit-btn rounded-lg bg-white/5 px-3 py-2 text-xs font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/10"
                                            data-edit="<?= e($editJson) ?>">
                                        Edit
                                    </button>
                                    <form method="post" action="/todos" class="inline"
                                          onsubmit="return confirm('Delete this task?');">
                                        <?php foreach ($stateAligned->hiddenInputs() as $h): ?>
                                            <input type="hidden" name="<?= e($h['name']) ?>" value="<?= e($h['value']) ?>"/>
                                        <?php endforeach; ?>
                                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"/>
                                        <input type="hidden" name="_action" value="delete"/>
                                        <input type="hidden" name="id" value="<?= (int) $todo['id'] ?>"/>
                                        <button type="submit"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold text-rose-300 ring-1 ring-rose-400/30 transition hover:bg-rose-500/10">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <nav class="mt-8" aria-label="Pagination">
            <div class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-center text-sm text-gray-500 sm:text-left">
                    Page <?= (int) $stateAligned->page ?> of <?= (int) $lastPage ?>
                </p>
                <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end">
                    <?php
                    $prevUrl = '/todos?' . $stateAligned->withPage(max(1, $stateAligned->page - 1))->fullQuery();
                    $nextUrl = '/todos?' . $stateAligned->withPage(min($lastPage, $stateAligned->page + 1))->fullQuery();
                    ?>
                    <a href="<?= e($prevUrl) ?>"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/15 text-white transition hover:bg-white/5 <?= $stateAligned->page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"
                       aria-label="Previous page">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                             aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                        </svg>
                    </a>
                    <div class="flex flex-wrap justify-center gap-1">
                        <?php
                        $winStart = max(1, $stateAligned->page - 2);
                        $winEnd = min($lastPage, $winStart + 4);
                        if (($winEnd - $winStart) < 4) {
                            $winStart = max(1, $winEnd - 4);
                        }
                        for ($pn = $winStart; $pn <= $winEnd; $pn++) {
                            $pu = '/todos?' . $stateAligned->withPage($pn)->fullQuery();
                            $isCur = $pn === $stateAligned->page;
                            ?>
                            <?php if ($isCur): ?>
                                <span class="min-w-9 rounded-lg bg-indigo-500 px-2 py-2 text-center text-sm font-medium text-white"
                                      aria-current="page"><?= $pn ?></span>
                            <?php else: ?>
                                <a href="<?= e($pu) ?>"
                                   class="min-w-9 rounded-lg border border-white/15 px-2 py-2 text-center text-sm font-medium text-white transition hover:bg-white/5"><?= $pn ?></a>
                            <?php endif; ?>
                        <?php } ?>
                    </div>
                    <a href="<?= e($nextUrl) ?>"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/15 text-white transition hover:bg-white/5 <?= $stateAligned->page >= $lastPage ? 'pointer-events-none opacity-40' : '' ?>"
                       aria-label="Next page">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                             aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5"/>
                        </svg>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <dialog id="dialog-form"
            class="fixed left-1/2 top-1/2 z-[200] m-0 hidden w-[min(100%,28rem)] max-w-[calc(100vw-2rem)] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-2xl border border-white/10 bg-gray-900 p-0 text-white shadow-2xl backdrop:bg-black/70 open:flex open:min-h-0 open:max-h-[min(90vh,calc(100dvh-2rem))] open:flex-col open:backdrop:backdrop-blur-[2px]">
        <form id="todo-form" method="post" action="/todos" class="flex min-h-0 flex-1 flex-col">
            <?php foreach ($stateAligned->hiddenInputs() as $h): ?>
                <input type="hidden" name="<?= e($h['name']) ?>" value="<?= e($h['value']) ?>"/>
            <?php endforeach; ?>
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"/>
            <input type="hidden" name="_action" id="todo-form-action" value="create"/>
            <input type="hidden" name="id" id="todo-form-id" value=""/>

            <div class="shrink-0 border-b border-white/10 px-5 py-4">
                <h2 id="todo-dialog-heading" class="text-lg font-semibold">Add todo</h2>
                <p class="text-xs text-gray-500">Tasks are saved to your account.</p>
            </div>
            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-4 text-sm">
                <div>
                    <label for="field-title" class="block text-xs font-medium text-gray-400">Title</label>
                    <input id="field-title" name="title" maxlength="500" required
                           class="mt-2 w-full rounded-xl border border-white/10 bg-gray-950 px-3 py-2.5 text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                           placeholder="What needs doing?"/>
                </div>
                <div>
                    <label for="field-notes" class="block text-xs font-medium text-gray-400">Notes <span
                                class="text-gray-600">(optional)</span></label>
                    <textarea id="field-notes" name="notes" rows="4" maxlength="20000"
                              class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-gray-950 px-3 py-2.5 text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                              placeholder="Context or sub-steps…"></textarea>
                </div>
                <fieldset>
                    <legend class="text-xs font-medium text-gray-400">Priority</legend>
                    <div class="mt-2 flex flex-wrap gap-3">
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="priority" value="low" id="prio-low"
                                   class="border-white/20 bg-gray-950 text-indigo-500 focus:ring-indigo-500/40"/>
                            Low
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="priority" value="med" id="prio-med" checked
                                   class="border-white/20 bg-gray-950 text-indigo-500 focus:ring-indigo-500/40"/>
                            Medium
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="priority" value="high" id="prio-high"
                                   class="border-white/20 bg-gray-950 text-indigo-500 focus:ring-indigo-500/40"/>
                            High
                        </label>
                    </div>
                </fieldset>
            </div>
            <div class="flex shrink-0 flex-wrap justify-end gap-2 border-t border-white/10 bg-gray-950/60 px-5 py-4">
                <button type="button" id="btn-form-cancel"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-300 ring-1 ring-white/15 hover:bg-white/5">
                    Cancel
                </button>
                <button type="submit" id="btn-form-save"
                        class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                    Save
                </button>
            </div>
        </form>
    </dialog>
</main>

<script>
    (() => {
        const dialog = document.getElementById('dialog-form');

        const openBtn = document.getElementById('btn-open-add');
        if (openBtn) {
            openBtn.addEventListener('click', () => {
                document.getElementById('field-title').value = '';
                document.getElementById('field-notes').value = '';
                document.getElementById('prio-med').checked = true;
                document.getElementById('todo-form-action').value = 'create';
                document.getElementById('todo-form-id').value = '';
                document.getElementById('todo-dialog-heading').textContent = 'Add todo';
                dialog.showModal();
            });
        }

        document.getElementById('btn-form-cancel').addEventListener('click', () => dialog.close());

        document.querySelectorAll('.todo-edit-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const raw = btn.getAttribute('data-edit');
                if (!raw) return;
                let data;
                try {
                    data = JSON.parse(raw);
                } catch {
                    return;
                }

                document.getElementById('todo-form-action').value = 'update';
                document.getElementById('todo-form-id').value = String(data.id);
                document.getElementById('field-title').value = data.title ?? '';
                document.getElementById('field-notes').value = data.notes ?? '';
                const p = (data.priority || 'med').toLowerCase();
                const map = {low: 'prio-low', med: 'prio-med', high: 'prio-high'};
                const id = map[p] || 'prio-med';
                document.getElementById(id).checked = true;
                document.getElementById('todo-dialog-heading').textContent = 'Edit todo';
                dialog.showModal();
            });
        });

        document.querySelectorAll('select.filter-auto').forEach((sel) => {
            sel.addEventListener('change', () => sel.closest('form')?.submit());
        });
    })();
</script>

<?php include_once __DIR__ . '/components/footer.php' ?>
