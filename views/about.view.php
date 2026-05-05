<?php include_once __DIR__ . '/components/head.php' ?>

<main class="min-h-[calc(100vh-81px)]">
    <!-- Hero -->
    <section class="relative isolate overflow-hidden px-6 pt-16 pb-16 sm:pt-24 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2 lg:gap-16 lg:items-center">
            <div>
                <p class="text-sm font-semibold text-indigo-400">Practice project</p>
                <h1 class="mt-3 text-pretty text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                    About this todo app
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-300">
                    A simple PHP + Tailwind playground for organizing tasks, trying routing and views without a heavy
                    framework—focused on clarity, small scope, and code you can actually read later.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="/"
                       class="rounded-md bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                        Back to home
                    </a>
                    <a href="/todos"
                       class="rounded-md px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/15 hover:bg-white/5">
                        View todos
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="aspect-[4/3] overflow-hidden rounded-2xl ring-1 ring-white/10 bg-gray-800">
                    <img
                            src="https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?q=80&w=1600&auto=format&fit=crop"
                            alt="Desk with notebook and pen—symbolizing planning and todos"
                            class="h-full w-full object-cover"
                            loading="lazy"
                            width="1600"
                            height="1200"
                    />
                </div>
                <p class="mt-3 text-xs text-gray-500">Photo via Unsplash (planning / notes).</p>
            </div>
        </div>
    </section>

    <!-- Principles -->
    <section class="border-t border-white/10 bg-gray-950/50 px-6 py-16 sm:py-20 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                    Built for learning, not spectacle
                </h2>
                <p class="mt-4 text-lg text-gray-300">
                    The goal is a maintainable skeleton: predictable routing, reusable partials, and UI that stays
                    consistent as you add real features later.
                </p>
            </div>
            <dl class="mt-12 grid max-w-7xl grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-white/10 bg-gray-900/60 p-6">
                    <dt class="flex items-center gap-2 text-base font-semibold text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300"
                              aria-hidden="true">/</span>
                        Simple routing
                    </dt>
                    <dd class="mt-2 text-sm leading-7 text-gray-400">
                        A small PHP entry point chooses which view to render so you always know where a URL goes.
                    </dd>
                </div>
                <div class="rounded-xl border border-white/10 bg-gray-900/60 p-6">
                    <dt class="flex items-center gap-2 text-base font-semibold text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300"
                              aria-hidden="true">◇</span>
                        Reusable UI pieces
                    </dt>
                    <dd class="mt-2 text-sm leading-7 text-gray-400">
                        Head, header, and footer partials avoid copy-pasting markup and keep the shell consistent page
                        to page.
                    </dd>
                </div>
                <div class="rounded-xl border border-white/10 bg-gray-900/60 p-6">
                    <dt class="flex items-center gap-2 text-base font-semibold text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300"
                              aria-hidden="true">✓</span>
                        Task-focused UX
                    </dt>
                    <dd class="mt-2 text-sm leading-7 text-gray-400">
                        The product direction stays practical: capture work, finish it, repeat—without extra dashboards
                        you don’t need yet.
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/components/footer.php' ?>
