<?php
/**
 * @var array{id:int,name:string,email:string,created_at:string}|null $profile
 * @var array{plan:string,expires_at:?string,granted_at:string}|null $subscriptionDetails
 * @var array{plan:string,expires_at:?string}|null $activeSubscription
 * @var string $noticeType
 * @var string $noticeMessage
 * @var int $freeLimit
 */

function esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function profile_plan_title(string $key): string
{
    try {
        return \App\Subscriptions\SubscriptionPlans::find($key)['title'];
    } catch (\Throwable) {
        return ucfirst($key);
    }
}

function profile_fmt_datetime(string $mysql): string
{
    $t = strtotime($mysql);

    return $t !== false ? date('M j, Y · g:i A', $t) : $mysql;
}

$hasActiveSubscription = $activeSubscription !== null;
$subscriptionRowPresent = $subscriptionDetails !== null;
$subscriptionExpired = $subscriptionRowPresent && !$hasActiveSubscription;
?>

<?php include_once __DIR__ . '/components/head.php' ?>

<main class="relative">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -right-28 top-12 h-80 w-80 rounded-full bg-indigo-500/18 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -left-36 bottom-8 h-72 w-72 rounded-full bg-violet-600/14 blur-3xl"
             aria-hidden="true"></div>
    </div>

    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:py-16">
        <header class="text-center sm:text-left">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-400">Your account</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                Profile
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-gray-400 sm:mx-0 sm:text-base">
                Manage your details and subscription. Cancelling removes unlimited access for this demo app immediately &mdash;
                your todos stay on your account.
            </p>
        </header>

        <?php if ($noticeMessage !== ''): ?>
            <?php $isErr = $noticeType === 'error'; ?>
            <div class="mx-auto mt-8 max-w-3xl rounded-xl border px-4 py-3 text-sm <?= $isErr ? 'border-red-500/35 bg-red-500/10 text-red-100' : 'border-emerald-500/35 bg-emerald-500/10 text-emerald-100' ?>"
                 role="<?= $isErr ? 'alert' : 'status' ?>">
                <p class="font-medium <?= $isErr ? 'text-white' : 'text-emerald-50' ?>"><?= esc($noticeMessage) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($profile === null): ?>
            <div class="mx-auto mt-10 rounded-2xl border border-white/10 bg-gray-950/60 px-6 py-10 text-center text-gray-400 ring-1 ring-white/5 backdrop-blur">
                <p class="text-base text-gray-300">We could not load your account from the database.</p>
                <p class="mt-3 text-sm">Try signing out and back in, or contact support if this persists.</p>
            </div>
        <?php else: ?>
            <div class="mt-10 space-y-8">
                <section aria-labelledby="profile-account-heading"
                         class="rounded-2xl border border-white/10 bg-gray-950/60 p-6 shadow-xl ring-1 ring-white/5 backdrop-blur sm:p-8">
                    <h2 id="profile-account-heading" class="text-lg font-semibold text-white">
                        Account
                    </h2>
                    <dl class="mt-6 grid gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Name</dt>
                            <dd class="mt-1 text-base text-gray-100"><?= esc($profile['name']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Email</dt>
                            <dd class="mt-1 break-all text-base text-gray-100"><?= esc($profile['email']) ?></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Member since</dt>
                            <dd class="mt-1 text-base text-gray-300"><?= esc(profile_fmt_datetime($profile['created_at'])) ?>
                                <span class="text-gray-500"> (server time)</span>
                            </dd>
                        </div>
                    </dl>
                </section>

                <section aria-labelledby="profile-sub-heading"
                         class="rounded-2xl border border-white/10 bg-gray-950/60 p-6 shadow-xl ring-1 ring-white/5 backdrop-blur sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <h2 id="profile-sub-heading" class="text-lg font-semibold text-white">
                            Subscription
                        </h2>
                        <a href="/subscribe"
                           class="inline-flex shrink-0 items-center justify-center rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                            View plans
                        </a>
                    </div>

                    <?php if ($hasActiveSubscription && $subscriptionDetails !== null && $activeSubscription !== null): ?>
                        <?php $lifetime = $activeSubscription['expires_at'] === null; ?>
                        <div class="mt-6 rounded-xl border border-emerald-500/25 bg-emerald-500/10 px-5 py-4 text-emerald-100 ring-1 ring-emerald-500/20">
                            <p class="font-semibold text-emerald-50">Active — <?= esc(profile_plan_title($activeSubscription['plan'])) ?></p>
                            <ul class="mt-3 space-y-2 text-sm text-emerald-100/90">
                                <li>
                                    <?php if ($lifetime): ?>
                                        Lifetime access · no renewal date
                                    <?php else: ?>
                                        Access through <?= esc(profile_fmt_datetime((string) $activeSubscription['expires_at'])) ?>
                                        <span class="text-emerald-200/70"> (server time)</span>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    Current period started <?= esc(profile_fmt_datetime($subscriptionDetails['granted_at'])) ?>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 border-t border-white/10 pt-8">
                            <h3 class="text-base font-semibold text-white">Cancel subscription</h3>
                            <p class="mt-2 max-w-prose text-sm leading-relaxed text-gray-400">
                                You will return to the free tier (<?= (int) $freeLimit ?> todos maximum). This demo ends access right away;
                                a production billing system would usually keep paid features until the billing period ends.
                            </p>
                            <form method="post" action="/profile" class="mt-6 space-y-4">
                                <input type="hidden" name="_token" value="<?= esc(csrf_token()) ?>"/>
                                <input type="hidden" name="_action" value="cancel_subscription"/>
                                <div class="flex items-start gap-3 rounded-lg bg-white/[0.04] px-4 py-3 ring-1 ring-white/10">
                                    <input id="confirm_cancel" type="checkbox" name="confirm_cancel" value="1"
                                           class="mt-1 size-4 rounded border-white/20 bg-gray-900 text-indigo-500 focus:ring-indigo-500/40"/>
                                    <label for="confirm_cancel" class="text-sm leading-snug text-gray-300">
                                        I understand unlimited todos will stop immediately and I may need to subscribe again to lift the cap.
                                    </label>
                                </div>
                                <button type="submit"
                                        class="rounded-lg bg-red-500/90 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/20 transition hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-400">
                                    End subscription access
                                </button>
                            </form>
                        </div>

                    <?php elseif ($subscriptionExpired && $subscriptionDetails !== null): ?>
                        <div class="mt-6 rounded-xl border border-amber-500/30 bg-amber-500/10 px-5 py-4 text-amber-100 ring-1 ring-amber-500/25">
                            <p class="font-semibold text-amber-50">Subscription expired</p>
                            <p class="mt-2 text-sm text-amber-100/90">
                                Last plan: <?= esc(profile_plan_title($subscriptionDetails['plan'])) ?>.
                                <?php if ($subscriptionDetails['expires_at'] !== null): ?>
                                    Access ended <?= esc(profile_fmt_datetime($subscriptionDetails['expires_at'])) ?>.
                                <?php endif; ?>
                            </p>
                            <p class="mt-3 text-sm text-amber-100/80">
                                You are on the free tier (<?= (int) $freeLimit ?> todos max).
                                <a href="/subscribe" class="font-semibold text-amber-200 underline decoration-amber-400/40 underline-offset-4 hover:text-amber-50">Renew or upgrade</a>.
                            </p>
                        </div>

                    <?php else: ?>
                        <div class="mt-6 rounded-xl border border-white/10 bg-white/[0.03] px-5 py-4 text-gray-300 ring-1 ring-white/10">
                            <p class="font-medium text-white">Free tier</p>
                            <p class="mt-2 text-sm leading-relaxed text-gray-400">
                                You can keep up to <?= (int) $freeLimit ?> todos.
                                Subscribe for unlimited todos and priority placement on this demo roadmap.
                            </p>
                            <p class="mt-4">
                                <a href="/subscribe"
                                   class="font-semibold text-indigo-300 underline decoration-indigo-400/40 underline-offset-4 hover:text-indigo-200">
                                    Compare subscription plans →
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/components/footer.php' ?>
