<?php
/**
 * Contact form view. Normally included from ContactController::index() with form state.
 * Defaults below keep the page safe if the file is opened in isolation and satisfy static analysis.
 */
$sent ??= false;
$errors ??= [];
$name ??= '';
$email ??= '';
$subject ??= '';
$message ??= '';

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>

<?php include_once __DIR__ . '/components/head.php' ?>

<main class="relative min-h-[calc(100vh-81px)]">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute right-0 top-0 h-72 w-72 translate-x-1/3 -translate-y-1/4 rounded-full bg-indigo-500/20 blur-3xl"
             aria-hidden="true"></div>
        <div class="absolute bottom-24 left-0 h-64 w-64 -translate-x-1/3 rounded-full bg-violet-600/15 blur-3xl"
             aria-hidden="true"></div>
    </div>

    <div class="mx-auto max-w-6xl px-4 pb-20 pt-10 sm:px-6 lg:px-8 lg:pb-28 lg:pt-16">
        <?php if ($sent): ?>
            <div class="mx-auto max-w-xl text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/20"
                     aria-hidden="true">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </div>
                <h1 class="mt-8 text-balance text-3xl font-semibold tracking-tight text-white sm:text-4xl">Message
                    received</h1>
                <p class="mt-4 text-lg text-gray-400">
                    Thanks for reaching out—this demo doesn’t deliver email yet; connect <code
                            class="rounded bg-gray-800 px-1.5 py-0.5 text-base text-gray-300">mail()</code> or your
                    transactional provider when you’re ready.
                </p>
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <a href="/contact"
                       class="rounded-xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                        Send another
                    </a>
                    <a href="/"
                       class="rounded-xl px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                        Back to home
                    </a>
                </div>
            </div>
        <?php else: ?>
            <header class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-400">Get in touch</p>
                <h1 class="mt-2 text-balance text-3xl font-semibold tracking-tight text-white sm:text-4xl">Contact</h1>
                <p class="mt-4 text-lg leading-relaxed text-gray-400">
                    Questions, feedback, or ideas send a note and we’ll get back when we can.
                </p>
            </header>

            <div class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-16 lg:items-start">
                <div class="lg:col-span-5">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Other ways</h2>
                    <ul class="mt-6 space-y-4" role="list">
                        <li>
                            <a href="mailto:hello@example.com"
                               class="group flex gap-4 rounded-2xl border border-white/10 bg-gray-950/60 p-4 transition hover:border-white/20 hover:bg-gray-900/80">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300 ring-1 ring-indigo-400/25"
                                      aria-hidden="true">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                         stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                    </svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block font-medium text-white group-hover:text-indigo-200">Email</span>
                                    <span class="mt-0.5 block text-sm text-gray-400">hello@example.com</span>
                                </span>
                            </a>
                        </li>
                        <li class="flex gap-4 rounded-2xl border border-white/10 bg-gray-950/60 p-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/5 text-gray-400 ring-1 ring-white/10"
                                  aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                     stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                            </span>
                            <span>
                                <span class="block font-medium text-white">Typical reply</span>
                                <span class="mt-0.5 block text-sm text-gray-400">Weekdays · within a few practice sessions</span>
                            </span>
                        </li>
                        <li class="flex gap-4 rounded-2xl border border-white/10 bg-gray-950/60 p-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/5 text-gray-400 ring-1 ring-white/10"
                                  aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                     stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                </svg>
                            </span>
                            <span>
                                <span class="block font-medium text-white">Location</span>
                                <span class="mt-0.5 block text-sm text-gray-400">Remote-first · UTC-friendly</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <section class="lg:col-span-7" aria-labelledby="contact-form-heading">
                    <div class="rounded-3xl border border-white/10 bg-gray-950/50 p-6 shadow-2xl ring-1 ring-white/5 backdrop-blur sm:p-8">
                        <h2 id="contact-form-heading" class="text-lg font-semibold text-white">Send a message</h2>
                        <p class="mt-1 text-sm text-gray-500">Required fields are marked.</p>

                        <?php if ($errors !== []): ?>
                            <div class="mt-6 rounded-xl border border-red-500/35 bg-red-500/10 px-4 py-3 text-sm text-red-100"
                                 role="alert">
                                <p class="font-medium text-white">Please check the highlighted fields below.</p>
                            </div>
                        <?php endif; ?>

                        <form class="mt-8 space-y-6" method="post" action="/contact" novalidate>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="contact-name" class="block text-sm font-medium text-gray-300">Name <span
                                                class="text-red-400" aria-hidden="true">*</span></label>
                                    <input id="contact-name" name="name" type="text" autocomplete="name" required
                                           maxlength="120"
                                           value="<?= e($name) ?>"
                                           class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/80 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['name']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                           aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>"
                                           aria-describedby="<?= isset($errors['name']) ? 'err-name' : '' ?>"/>
                                    <?php if (isset($errors['name'])): ?>
                                        <p id="err-name"
                                           class="mt-1.5 text-xs text-red-300"><?= e($errors['name']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label for="contact-email" class="block text-sm font-medium text-gray-300">Email
                                        <span class="text-red-400" aria-hidden="true">*</span></label>
                                    <input id="contact-email" name="email" type="email" autocomplete="email" required
                                           maxlength="254"
                                           value="<?= e($email) ?>"
                                           class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/80 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['email']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                           aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>"
                                           aria-describedby="<?= isset($errors['email']) ? 'err-email' : '' ?>"/>
                                    <?php if (isset($errors['email'])): ?>
                                        <p id="err-email"
                                           class="mt-1.5 text-xs text-red-300"><?= e($errors['email']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <label for="contact-subject" class="block text-sm font-medium text-gray-300">Topic <span
                                            class="text-xs font-normal text-gray-600">(optional)</span></label>
                                <select id="contact-subject" name="subject"
                                        class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/80 px-4 py-3 text-sm text-white focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                    <?php
                                    $topics = [
                                            '' => 'Select a topic…',
                                            'feedback' => 'Product feedback',
                                            'support' => 'Help & support',
                                            'billing' => 'Billing',
                                            'other' => 'Something else',
                                    ];
                                    foreach ($topics as $val => $label) {
                                        $sel = $subject === $val ? ' selected' : '';
                                        echo '<option value="' . e($val) . '"' . $sel . '>' . e($label) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="contact-message" class="block text-sm font-medium text-gray-300">Message
                                    <span class="text-red-400" aria-hidden="true">*</span></label>
                                <textarea id="contact-message" name="message" rows="5" required maxlength="4000"
                                          class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-gray-900/80 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['message']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                          placeholder="What’s on your mind?"
                                          aria-invalid="<?= isset($errors['message']) ? 'true' : 'false' ?>"
                                          aria-describedby="<?= isset($errors['message']) ? 'err-msg' : '' ?>"><?= e($message) ?></textarea>
                                <?php if (isset($errors['message'])): ?>
                                    <p id="err-msg" class="mt-1.5 text-xs text-red-300"><?= e($errors['message']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col gap-4 border-t border-white/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-gray-500">Demo form—friendly, non-commercial use.</p>
                                <button type="submit"
                                        class="w-full shrink-0 rounded-xl bg-indigo-500 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400 sm:w-auto">
                                    Send message
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/components/footer.php' ?>
