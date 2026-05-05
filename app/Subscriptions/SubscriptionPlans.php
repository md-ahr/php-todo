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

    /** @return list<array{key:string,title:string,copy:string,usd:number,period:string}> */
    public static function catalog(): array
    {
        return [
            [
                'key' => self::MONTHLY,
                'title' => 'Monthly',
                'copy' => 'Full access billed every month.',
                'usd' => 5,
                'period' => 'per month',
            ],
            [
                'key' => self::YEARLY,
                'title' => 'Yearly',
                'copy' => 'Best value — two months free vs monthly.',
                'usd' => 50,
                'period' => 'per year',
            ],
            [
                'key' => self::LIFETIME,
                'title' => 'Lifetime',
                'copy' => 'Pay once — unlimited todos with no renewal.',
                'usd' => 200,
                'period' => 'one-time',
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
