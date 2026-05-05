<?php

declare(strict_types=1);

namespace App\Subscriptions;

final class SubscriptionPlans
{
    public const MONTHLY = 'monthly';

    public const YEARLY = 'yearly';

    public const LIFETIME = 'lifetime';

    /** @var list<string> */
    private const KEYS = [self::MONTHLY, self::YEARLY, self::LIFETIME];

    /** @return list<array{key:string,title:string,copy:string,usd:number,period:string,features:list<string>}> */
    public static function catalog(): array
    {
        return [
            [
                'key' => self::MONTHLY,
                'title' => 'Monthly',
                'copy' => 'Unlimited todos on a simple month-to-month plan. Best if you want full access without committing to a longer term.',
                'usd' => 5,
                'period' => 'per month',
                'features' => [
                    'No cap on how many todos you can create',
                    'Renews monthly — pause or switch plans anytime',
                ],
            ],
            [
                'key' => self::YEARLY,
                'title' => 'Yearly',
                'copy' => 'The same unlimited access, billed once a year. You pay for about ten months instead of twelve when compared to monthly.',
                'usd' => 50,
                'period' => 'per year',
                'features' => [
                    'Everything in Monthly, at a lower effective price',
                    'One charge per year — less to think about',
                ],
            ],
            [
                'key' => self::LIFETIME,
                'title' => 'Lifetime',
                'copy' => 'A single payment covers unlimited todos for as long as you use the app — no subscription renewals or future price hikes for this tier.',
                'usd' => 200,
                'period' => 'one-time',
                'features' => [
                    'Unlimited todos with no expiry on your access',
                    'Pay once — no recurring charges from this plan',
                ],
            ],
        ];
    }

    public static function isKnownPlan(string $key): bool
    {
        return in_array($key, self::KEYS, true);
    }

    /** @throws \InvalidArgumentException */
    public static function assertKnownPlan(string $key): string
    {
        if (!self::isKnownPlan($key)) {
            throw new \InvalidArgumentException('Unknown subscription plan.');
        }

        return $key;
    }

    /** @throws \RuntimeException */
    public static function find(string $key): array
    {
        self::assertKnownPlan($key);
        foreach (self::catalog() as $row) {
            if ($row['key'] === $key) {
                return $row;
            }
        }

        throw new \RuntimeException('Plan metadata missing.');
    }
}
