<?php
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);
if ($path === false || $path === '' || $path === null) {
    $path = '/';
}

$authUser = auth_user();
?>

<header class="bg-gray-900 border-b sticky top-0 z-10">
    <nav aria-label="Global" class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8">
        <div class="flex lg:flex-1">
            <a href="/" aria-label="ToDo App — Home"
               class="-m-1.5 flex items-center gap-2 rounded-md p-1.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="todo-logo-grad" x1="4" y1="3" x2="30" y2="31"
                                        gradientUnits="userSpaceOnUse">
                            <stop stop-color="#A5B4FC"/>
                            <stop offset="1" stop-color="#6366F1"/>
                        </linearGradient>
                    </defs>
                    <rect width="32" height="32" rx="9" fill="url(#todo-logo-grad)"/>
                    <rect x="0.5" y="0.5" width="31" height="31" rx="8.5" stroke="#FFFFFF" stroke-opacity="0.18"/>
                    <!-- Checklist motif: accent lines -->
                    <path d="M7 22.5h10" stroke="#312E81" stroke-opacity="0.35" stroke-width="1.75"
                          stroke-linecap="round"/>
                    <path d="M7 25.75h14" stroke="#312E81" stroke-opacity="0.25" stroke-width="1.75"
                          stroke-linecap="round"/>
                    <!-- Done check -->
                    <path d="M8 11l4 4 8-10" stroke="#FFFFFF" stroke-width="2.25" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
                <span class="text-lg font-semibold tracking-tight text-white" aria-hidden="true">ToDo</span>
            </a>
        </div>
        <div class="flex lg:hidden">
            <button type="button" command="show-modal" commandfor="mobile-menu"
                    class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-400">
                <span class="sr-only">Open main menu</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                     aria-hidden="true" class="size-6">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <el-popover-group class="hidden lg:flex lg:gap-x-12">
            <a href="/"
               class="text-sm/6 font-semibold hover:text-blue-500 transition <?= $path === '/' ? 'text-blue-500' : 'text-white' ?>">Home</a>
            <a href="/todos"
               class="text-sm/6 font-semibold hover:text-blue-500 transition <?= $path === '/todos' ? 'text-blue-500' : 'text-white' ?>">Todos</a>
            <a href="/about"
               class="text-sm/6 font-semibold hover:text-blue-500 transition <?= $path === '/about' ? 'text-blue-500' : 'text-white' ?>">About
                Us</a>
            <a href="/contact"
               class="text-sm/6 font-semibold hover:text-blue-500 transition <?= $path === '/contact' ? 'text-blue-500' : 'text-white' ?>">Contact</a>
        </el-popover-group>
        <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-end lg:gap-x-4">
            <?php if ($authUser !== null): ?>
                <span class="max-w-[14rem] truncate text-sm text-gray-300"
                      title="<?= htmlspecialchars($authUser['email'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($authUser['name'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <form method="post" action="/logout" class="inline-block">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>"/>
                    <button type="submit"
                            class="rounded-md text-sm/6 font-semibold text-white ring-1 ring-white/15 px-3 py-1.5 transition hover:bg-white/5 hover:text-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                        Sign out
                    </button>
                </form>
            <?php else: ?>
                <a href="/login"
                   class="text-sm/6 font-semibold transition hover:text-blue-500 <?= $path === '/login' ? 'text-blue-500' : 'text-white' ?>">Log
                    in <span aria-hidden="true">&rarr;</span></a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <el-dialog>
        <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel
                        class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-gray-900 p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-100/10">
                    <div class="flex items-center justify-between">
                        <a href="/" aria-label="ToDo App — Home"
                           class="-m-1.5 flex items-center gap-2 rounded-md p-1.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="todo-logo-grad" x1="4" y1="3" x2="30" y2="31"
                                                    gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#A5B4FC"/>
                                        <stop offset="1" stop-color="#6366F1"/>
                                    </linearGradient>
                                </defs>
                                <rect width="32" height="32" rx="9" fill="url(#todo-logo-grad)"/>
                                <rect x="0.5" y="0.5" width="31" height="31" rx="8.5" stroke="#FFFFFF"
                                      stroke-opacity="0.18"/>
                                <!-- Checklist motif: accent lines -->
                                <path d="M7 22.5h10" stroke="#312E81" stroke-opacity="0.35" stroke-width="1.75"
                                      stroke-linecap="round"/>
                                <path d="M7 25.75h14" stroke="#312E81" stroke-opacity="0.25" stroke-width="1.75"
                                      stroke-linecap="round"/>
                                <!-- Done check -->
                                <path d="M8 11l4 4 8-10" stroke="#FFFFFF" stroke-width="2.25" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <span class="text-lg font-semibold tracking-tight text-white" aria-hidden="true">ToDo</span>
                        </a>
                        <button type="button" command="close" commandfor="mobile-menu"
                                class="-m-2.5 rounded-md p-2.5 text-gray-400">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                 data-slot="icon" aria-hidden="true" class="size-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-white/10">
                            <div class="space-y-2 py-6">
                                <a href="/"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold hover:bg-white/5 <?= $path === '/' ? 'text-blue-500' : 'text-white' ?>">Home</a>
                                <a href="/todos"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold hover:bg-white/5 <?= $path === '/todos' ? 'text-blue-500' : 'text-white' ?>">Todos</a>
                                <a href="/about"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold hover:bg-white/5 <?= $path === '/about' ? 'text-blue-500' : 'text-white' ?>">About
                                    Us</a>
                                <a href="/contact"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold hover:bg-white/5 <?= $path === '/contact' ? 'text-blue-500' : 'text-white' ?>">Contact</a>
                            </div>
                            <div class="space-y-2 py-6">
                                <?php if ($authUser !== null): ?>
                                    <p class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-gray-400">
                                        Signed in as
                                        <span class="block font-semibold text-white truncate"><?= htmlspecialchars($authUser['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </p>
                                    <form method="post" action="/logout" class="-mx-3 block px-3">
                                        <input type="hidden" name="_token"
                                               value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>"/>
                                        <button type="submit"
                                                class="w-full rounded-lg py-2.5 text-left text-base/7 font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/5">
                                            Sign out
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="/login"
                                       class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold hover:bg-white/5 <?= $path === '/login' ? 'text-blue-500' : 'text-white' ?>">Log
                                        in</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>
