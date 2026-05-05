<?php

declare(strict_types=1);

namespace App\Subscriptions;

use App\Repositories\UserSubscriptionRepository;

/**
 * Free tier caps total todos unless the user has an active subscription row.
 */
final class TodoQuota
{
    private function __construct()
    {
    }

    public static function freeTodoLimit(): int
    {
        $v = env('FREE_TODO_LIMIT', 10);
        if (is_numeric($v)) {
            $n = (int) $v;
            if ($n >= 1) {
                return $n;
            }
        }

        return 10;
    }

    /**
     * @return array{
     *   limit:int,
     *   total:int,
     *   subscribed:bool,
     *   can_create:bool,
     *   remaining:int
     * }
     */
    public static function assess(UserSubscriptionRepository $subs, int $userId, int $totalTodos): array
    {
        $limit = self::freeTodoLimit();
        $subscribed = $subs->hasActiveSubscription($userId);
        $canCreate = $subscribed || ($totalTodos < $limit);

        return [
            'limit' => $limit,
            'total' => $totalTodos,
            'subscribed' => $subscribed,
            'can_create' => $canCreate,
            'remaining' => max(0, $limit - $totalTodos),
        ];
    }

    /** When payment integration exists, simulations should remain off outside local/dev. */
    public static function allowSimulatedCheckout(): bool
    {
        return (bool) env('APP_SIMULATE_SUBSCRIPTION_CHECKOUT', true);
    }
}
