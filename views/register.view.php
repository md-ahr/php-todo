<?php
/**
 * Registration form. Populated by RegisterController.
 */
$errors ??= [];
$name ??= '';
$email ??= '';
$termsChecked ??= false;

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
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
                        aria-labelledby="register-heading">
                    <div class="flex items-start justify-between gap-4">
                        <h2 id="register-heading" class="text-xl font-semibold text-white">Create account</h2>

                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 text-gray-400 ring-1 ring-white/10"
                              aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                        </span>
                    </div>

                    <?php if ($errors !== []): ?>
                        <div class="mt-6 rounded-xl border border-red-500/35 bg-red-500/10 px-4 py-3 text-sm text-red-100"
                             role="alert">
                            <p class="font-medium text-white">Please fix the issues below and try again.</p>
                            <?php if (isset($errors['_form'])): ?>
                                <p class="mt-2 text-red-200"><?= e($errors['_form']) ?></p>
                            <?php endif; ?>
                            <?php if (isset($errors['_csrf'])): ?>
                                <p class="mt-2 text-red-200"><?= e($errors['_csrf']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form class="mt-8 space-y-6" action="/register" method="post" autocomplete="on" novalidate>
                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"/>

                        <div>
                            <label for="register-name" class="block text-sm font-medium text-gray-300">Name <span
                                        class="text-red-400" aria-hidden="true">*</span></label>
                            <input id="register-name" name="name" type="text" autocomplete="name"
                                   maxlength="255"
                                   value="<?= e($name) ?>"
                                   class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/90 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['name']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                   placeholder="Jane Doe"
                                   aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['name']) ? 'err-register-name' : '' ?>"/>
                            <?php if (isset($errors['name'])): ?>
                                <p id="err-register-name" class="mt-1.5 text-xs text-red-300"><?= e($errors['name']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="register-email" class="block text-sm font-medium text-gray-300">Email <span
                                        class="text-red-400" aria-hidden="true">*</span></label>
                            <input id="register-email" name="email" type="email" autocomplete="email"
                                   maxlength="254"
                                   value="<?= e($email) ?>"
                                   class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/90 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['email']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                   placeholder="you@example.com"
                                   aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['email']) ? 'err-register-email' : '' ?>"/>
                            <?php if (isset($errors['email'])): ?>
                                <p id="err-register-email"
                                   class="mt-1.5 text-xs text-red-300"><?= e($errors['email']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="register-password" class="block text-sm font-medium text-gray-300">Password
                                <span class="text-red-400" aria-hidden="true">*</span></label>
                            <input id="register-password" name="password" type="password" autocomplete="new-password"
                                   minlength="8"
                                   maxlength="72"
                                   class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/90 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['password']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                   placeholder="••••••••"
                                   aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['password']) ? 'err-register-password' : '' ?>"/>
                            <?php if (isset($errors['password'])): ?>
                                <p id="err-register-password"
                                   class="mt-1.5 text-xs text-red-300"><?= e($errors['password']) ?></p>
                            <?php endif; ?>
                            <p class="mt-1.5 text-xs text-gray-500">At least 8 characters.</p>
                        </div>

                        <div>
                            <label for="register-password-confirm" class="block text-sm font-medium text-gray-300">Confirm
                                password <span class="text-red-400" aria-hidden="true">*</span></label>
                            <input id="register-password-confirm" name="password_confirmation" type="password"
                                   autocomplete="new-password"
                                   minlength="8"
                                   maxlength="72"
                                   class="mt-2 w-full rounded-xl border border-white/10 bg-gray-900/90 px-4 py-3 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 <?= isset($errors['password_confirmation']) ? 'border-red-500/50 ring-1 ring-red-500/30' : '' ?>"
                                   placeholder="••••••••"
                                   aria-invalid="<?= isset($errors['password_confirmation']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['password_confirmation']) ? 'err-register-confirm' : '' ?>"/>
                            <?php if (isset($errors['password_confirmation'])): ?>
                                <p id="err-register-confirm"
                                   class="mt-1.5 text-xs text-red-300"><?= e($errors['password_confirmation']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-start gap-3">
                            <input id="register-terms" name="terms" type="checkbox"
                                   <?= $termsChecked ? 'checked ' : '' ?>
                                   class="mt-1 h-4 w-4 shrink-0 rounded border-white/20 bg-gray-950 text-indigo-500 focus:ring-indigo-500/40"
                                   aria-invalid="<?= isset($errors['terms']) ? 'true' : 'false' ?>"
                                   aria-describedby="<?= isset($errors['terms']) ? 'err-register-terms' : '' ?>"/>
                            <label for="register-terms" class="text-sm leading-snug text-gray-400">
                                I agree to the <a href="#"
                                                   class="text-indigo-400 underline-offset-2 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 rounded">terms</a>
                                and <a href="#"
                                       class="text-indigo-400 underline-offset-2 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 rounded">privacy
                                    policy</a>. <span class="text-red-400" aria-hidden="true">*</span>
                            </label>
                        </div>
                        <?php if (isset($errors['terms'])): ?>
                            <p id="err-register-terms"
                               class="-mt-2 text-xs text-red-300 lg:ml-7"><?= e($errors['terms']) ?></p>
                        <?php endif; ?>

                        <button type="submit"
                                class="w-full rounded-xl bg-indigo-500 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                            Create account
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Already have an account?
                        <a href="/login"
                           class="font-semibold text-indigo-400 hover:text-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 rounded">
                            Sign in
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
