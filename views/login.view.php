<?php
/**
 * Login form — LoginController passes state.
 */
$registered ??= false;
$errors ??= [];
$credentialsMessage ??= '';
$email ??= '';

if (!function_exists('e')) {
    function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}
?>

<?php include_once __DIR__ . '/components/head.php' ?>

<main class="relative">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -right-32 top-0 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -left-40 bottom-0 h-80 w-80 rounded-full bg-violet-600/12 blur-3xl"
             aria-hidden="true"></div>
    </div>

    <div class="mx-auto flex max-w-lg flex-col justify-center px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid w-full items-center gap-12 lg:grid-cols-[1fr,minmax(0,26rem)] lg:gap-16 xl:gap-24">
            <div class="w-full lg:justify-self-end">
                <section
                        class="rounded-3xl border border-white/10 bg-gray-950/60 p-6 shadow-2xl ring-1 ring-white/5 backdrop-blur sm:p-8"
                        aria-labelledby="login-heading">
                    <div class="flex items-start justify-between gap-4">
                        <h2 id="login-heading" class="text-xl font-semibold text-white">Log in</h2>

                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 text-gray-400 ring-1 ring-white/10"
                              aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 9V8.25A3.75 3.75 0 0 0 12 4.5H6A2.25 2.25 0 0 0 4.5 6.75v10.5A2.25 2.25 0 0 0 6 19.5h6a3.75 3.75 0 0 0 3.75-3.75V15M18 15l3-3m0 0-3-3m3 3H9"/>
                            </svg>
                        </span>
                    </div>

                    <?php if ($registered): ?>
                        <div class="mt-6 rounded-xl border border-emerald-500/35 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100"
                             role="status">
                            <p class="font-medium text-emerald-50">Account created</p>
                            <p class="mt-1 text-emerald-200/95">Sign in below with your new credentials.</p>
                        </div>
                    <?php endif; ?>

                    <?php
                    $loginFieldErrors = isset($errors['email']) || isset($errors['password']);
                    ?>
                    <?php if (isset($errors['_csrf'])): ?>
                        <div class="mt-6 rounded-xl border border-red-500/35 bg-red-500/10 px-4 py-3 text-sm text-red-100"
                             role="alert">
                            <p><?= e($errors['_csrf']) ?></p>
                        </div>
                    <?php elseif ($credentialsMessage !== ''): ?>
                        <div class="mt-6 rounded-xl border border-red-500/35 bg-red-500/10 px-4 py-3 text-sm text-red-100"
                             role="alert">
                            <p class="font-medium text-white">Sign-in failed</p>
                            <p class="mt-1 text-red-200"><?= e($credentialsMessage) ?></p>
                        </div>
                    <?php elseif ($loginFieldErrors): ?>
                        <div class="mt-6 rounded-xl border border-red-500/35 bg-red-500/10 px-4 py-3 text-sm text-red-100"
                             role="alert">
                            <p class="font-medium text-white">Please check the highlighted fields below.</p>
                        </div>
                    <?php endif; ?>

                    <form class="mt-8 space-y-6" action="/login" method="post" autocomplete="on" novalidate>
                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"/>

                        <div>
                            <label for="login-email" class="block text-sm font-medium text-gray-300">Email <span
                                        class="text-red-400" aria-hidden="true">*</span></label>
                            <input id="login-email" name="email" type="email" autocomplete="username"
                                   maxlength="254"
                                   value="<?= e($email) ?>"
                                   class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/90 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['email']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                   placeholder="you@example.com"
                                   aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['email']) ? 'err-login-email' : '' ?>"/>
                            <?php if (isset($errors['email'])): ?>
                                <p id="err-login-email" class="mt-1.5 text-xs text-red-300"><?= e($errors['email']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <label for="login-password"
                                       class="block text-sm font-medium text-gray-300">Password <span
                                            class="text-red-400" aria-hidden="true">*</span></label>
                            </div>
                            <input id="login-password" name="password" type="password"
                                   autocomplete="current-password"
                                   class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/90 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['password']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                   placeholder="••••••••"
                                   aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['password']) ? 'err-login-password' : '' ?>"/>
                            <?php if (isset($errors['password'])): ?>
                                <p id="err-login-password"
                                   class="mt-1.5 text-xs text-red-300"><?= e($errors['password']) ?></p>
                            <?php endif; ?>
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-indigo-500 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                            Sign in
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        New here?
                        <a href="/register"
                           class="font-semibold text-indigo-400 hover:text-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 rounded">
                            Create an account
                        </a>
                    </p>
                </section>

                <p class="mt-6 text-center text-xs text-gray-600 lg:text-left">
                    <a href="/"
                       class="text-gray-400 transition hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white rounded">
                        ← Back to home
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/components/footer.php' ?>
