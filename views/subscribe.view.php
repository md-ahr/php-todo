<?php
/**
 * @var list<array{key:string,title:string,copy:string,usd:int,period:string,features:list<string>}> $plans
 * @var array{plan:string,expires_at:?string}|null $active
 * @var string $noticeType
 * @var string $noticeMessage
 */
$simulate = \App\Subscriptions\TodoQuota::allowSimulatedCheckout();

function esc(string $s): string
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

    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-16">
        <header class="text-center lg:text-left">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-400">Membership</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                Unlock unlimited todos
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-gray-400 sm:text-base lg:mx-0">
                Stay on the free tier with <?= (int) \App\Subscriptions\TodoQuota::freeTodoLimit() ?> todos, or subscribe
                for unlimited tasks.
                <?php if ($simulate): ?>
                    <span class="mt-2 block text-amber-200/90">Practice mode uses instant checkout — no real payment processor.</span>
                <?php endif; ?>
            </p>
        </header>

        <?php if ($noticeMessage !== ''): ?>
            <?php $isErr = $noticeType === 'error'; ?>
            <div class="mx-auto mt-8 max-w-3xl rounded-xl border px-4 py-3 text-sm <?= $isErr ? 'border-red-500/35 bg-red-500/10 text-red-100' : 'border-emerald-500/35 bg-emerald-500/10 text-emerald-100' ?>"
                 role="<?= $isErr ? 'alert' : 'status' ?>">
                <p class="font-medium <?= $isErr ? 'text-white' : 'text-emerald-50' ?>"><?= esc($noticeMessage) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($active !== null): ?>
            <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-white/12 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-100 ring-1 ring-emerald-500/25">
                <p class="font-semibold text-emerald-50">Your subscription is active</p>
                <p class="mt-2 text-emerald-100/90">
                    Plan: <?= esc(ucfirst($active['plan'])) ?>
                    <?php if ($active['expires_at'] === null): ?>
                        — Lifetime access · no expiry
                    <?php else: ?>
                        — Renews <?= esc(date('M j, Y · g:i A', strtotime($active['expires_at']))) ?> (server time)
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php $lifetimeHeld = $active !== null && $active['expires_at'] === null; ?>

        <div class="mx-auto mt-12 grid gap-6 lg:grid-cols-3">
            <?php foreach ($plans as $plan): ?>
                <?php $planKey = $plan['key']; ?>
                <section
                        class="flex h-full flex-col rounded-2xl border border-white/10 bg-gray-950/60 p-6 shadow-xl ring-1 ring-white/5 backdrop-blur">
                    <div class="flex flex-1 flex-col">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-white">$<?= (int) $plan['usd'] ?></span>
                            <span class="text-sm font-medium uppercase tracking-wide text-gray-400"><?= esc($plan['period']) ?></span>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold text-white"><?= esc($plan['title']) ?></h2>
                        <div class="mt-2 flex min-h-0 flex-1 flex-col">
                            <p class="text-sm leading-relaxed text-gray-400"><?= esc($plan['copy']) ?></p>

                            <?php if (($plan['features'] ?? []) !== []): ?>
                                <ul class="mt-4 space-y-2.5 text-sm text-gray-300" role="list">
                                    <?php foreach ($plan['features'] as $feature): ?>
                                        <li class="flex gap-2.5">
                                            <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                            <span><?= esc($feature) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <?php if (!$simulate): ?>
                            <p class="mt-6 rounded-lg bg-white/5 px-4 py-3 text-xs text-gray-400 ring-1 ring-white/10">
                                Real payments disabled. Set APP_SIMULATE_SUBSCRIPTION_CHECKOUT=true for development, or integrate a payment provider.
                            </p>
                        <?php elseif ($lifetimeHeld): ?>
                            <p class="mt-6 text-center text-sm font-medium text-emerald-200">
                                Included with your lifetime membership
                            </p>
                        <?php else: ?>
                            <form method="post" action="/subscribe" class="mt-6">
                                <input type="hidden" name="_token" value="<?= esc(csrf_token()) ?>"/>
                                <input type="hidden" name="plan" value="<?= esc($planKey) ?>"/>
                                <button type="submit"
                                        class="w-full rounded-lg bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                                    <?php
                                    $sameActive = !$lifetimeHeld && $active !== null && $active['plan'] === $planKey;
                                    if ($sameActive) {
                                        echo 'Extend ' . esc($plan['title']);
                                    } elseif ($active !== null) {
                                        echo 'Switch / extend · ' . esc($plan['title']);
                                    } else {
                                        echo 'Choose ' . esc($plan['title']);
                                    }
                                    ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/components/footer.php' ?>
