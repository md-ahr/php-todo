<?php include_once __DIR__ . '/components/head.php' ?>

<main>
    <!-- Hero -->
    <section class="relative isolate overflow-hidden px-6 pt-12 pb-20 sm:pt-16 sm:pb-28 lg:px-8 lg:pb-36">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 h-[420px] w-[420px] translate-x-1/3 -translate-y-1/4 rounded-full bg-indigo-500/20 blur-3xl sm:h-[520px] sm:w-[520px]"
                 aria-hidden="true"></div>
            <div class="absolute bottom-0 left-0 h-[320px] w-[320px] -translate-x-1/3 translate-y-1/4 rounded-full bg-violet-600/15 blur-3xl"
                 aria-hidden="true"></div>
        </div>

        <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2 lg:items-center lg:gap-16">
            <div class="mx-auto max-w-2xl text-center lg:mx-0 lg:max-w-none lg:text-left">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-400">Stay on top of your day</p>
                <h1 class="mt-4 text-pretty text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    A calmer place to capture and finish what matters.
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-300">
                    Capture tasks quickly, stay organized with a simple workflow, and keep this project easy to evolve as you practice PHP routing and reusable views.
                </p>
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4 lg:justify-start">
                    <a href="/todos"
                       class="rounded-lg bg-indigo-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                        Open your todos
                    </a>
                    <a href="/about"
                       class="rounded-lg px-5 py-3 text-sm font-semibold text-white ring-1 ring-inset ring-white/15 transition hover:bg-white/5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                        How this app works
                    </a>
                </div>
                <dl class="mt-12 grid grid-cols-2 gap-6 text-left sm:max-w-md sm:grid-cols-3 lg:max-w-none">
                    <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm">
                        <dt class="text-xs font-medium text-gray-400">Focus</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">One list, clear flow</dd>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm">
                        <dt class="text-xs font-medium text-gray-400">Speed</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Add tasks in seconds</dd>
                    </div>
                    <div class="col-span-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm sm:col-span-1">
                        <dt class="text-xs font-medium text-gray-400">Growth</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Practice-friendly codebase</dd>
                    </div>
                </dl>
            </div>

            <div class="mx-auto w-full max-w-lg lg:mx-0 lg:max-w-none">
                <div class="rounded-2xl border border-white/10 bg-gray-950/80 p-1 shadow-2xl ring-1 ring-white/10 backdrop-blur-md">
                    <div class="rounded-xl bg-gray-900/90 p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                            <div>
                                <p class="text-sm font-semibold text-white">Today</p>
                                <p class="text-xs text-gray-500"><?php echo date('l, M j'); ?></p>
                            </div>
                            <span class="rounded-full bg-indigo-500/15 px-2.5 py-1 text-xs font-medium text-indigo-300">3 active</span>
                        </div>
                        <ul class="mt-5 space-y-3" aria-label="Sample tasks preview">
                            <li class="flex items-start gap-3 rounded-lg border border-white/5 bg-white/[0.03] p-3">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border border-indigo-400/50 bg-indigo-500/20"
                                      aria-hidden="true">
                                    <svg class="h-3 w-3 text-indigo-200" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                        <path d="M10 3L4.5 8.5 2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-200 line-through decoration-white/30">Sketch home page layout</p>
                                    <p class="mt-0.5 text-xs text-gray-500">Design</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 rounded-lg border border-white/5 bg-white/[0.03] p-3">
                                <span class="mt-0.5 h-5 w-5 shrink-0 rounded border border-white/20" aria-hidden="true"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white">Wire up todos route</p>
                                    <p class="mt-0.5 text-xs text-gray-500">Development</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 rounded-lg border border-white/5 bg-white/[0.03] p-3">
                                <span class="mt-0.5 h-5 w-5 shrink-0 rounded border border-white/20" aria-hidden="true"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white">Polish responsive footer</p>
                                    <p class="mt-0.5 text-xs text-gray-500">UI</p>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-5 flex items-center gap-2 rounded-lg border border-dashed border-white/15 bg-white/[0.02] px-3 py-2.5 text-sm text-gray-500">
                            <span class="text-lg leading-none text-gray-600" aria-hidden="true">+</span>
                            <span>Add a task…</span>
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-center text-xs text-gray-500 lg:text-left">Preview only - open Todos to use the real list.</p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="border-t border-white/10 bg-gray-950/40 px-6 py-16 sm:py-20 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mx-auto max-w-2xl text-center lg:mx-auto lg:max-w-none lg:text-center">
                <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                    Built to feel effortless
                </h2>
                <p class="mt-4 text-lg text-gray-400">
                    A small surface area keeps the UX honest: fewer knobs, quicker wins, easier maintenance.
                </p>
            </div>
            <ul class="mx-auto mt-12 grid max-w-5xl grid-cols-1 gap-6 sm:grid-cols-2 lg:mt-14 lg:max-w-none lg:grid-cols-3 lg:gap-8">
                <li class="rounded-2xl border border-white/10 bg-gray-900/50 p-6 transition hover:border-white/20 hover:bg-gray-900/70">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300"
                         aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.75 6h16.5M3.75 12h16.5M3.75 18h16.5"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-white">Clarity first</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-400">
                        Big type, restrained color, and plenty of spacing so the homepage reads well on phones and desktops alike.
                    </p>
                </li>
                <li class="rounded-2xl border border-white/10 bg-gray-900/50 p-6 transition hover:border-white/20 hover:bg-gray-900/70">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300"
                         aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-white">Task flow</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-400">
                        The preview card mirrors how you’ll work: mark items done, scan what’s next, and add new work in one calm column.
                    </p>
                </li>
                <li class="rounded-2xl border border-white/10 bg-gray-900/50 p-6 transition hover:border-white/20 hover:bg-gray-900/70 sm:col-span-2 lg:col-span-1">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300"
                         aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-white">Responsive by default</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-400">
                        Grids collapse gracefully, CTAs wrap on narrow screens, and the hero balances copy with the preview panel.
                    </p>
                </li>
            </ul>
        </div>
    </section>

    <!-- CTA -->
    <section class="px-6 py-16 sm:py-20 lg:px-8">
        <div class="mx-auto max-w-4xl rounded-2xl border border-white/10 bg-gradient-to-br from-indigo-500/10 via-gray-900 to-violet-600/10 px-6 py-12 text-center sm:px-10 sm:py-14">
            <h2 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Ready when you are</h2>
            <p class="mt-4 text-gray-400">
                Jump into the list or read the About page to see how the project is structured.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="/todos"
                   class="rounded-lg bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    Go to Todos
                </a>
                <a href="/login"
                   class="text-sm font-semibold text-white hover:text-indigo-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400 rounded">
                    Log in <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/components/footer.php' ?>
