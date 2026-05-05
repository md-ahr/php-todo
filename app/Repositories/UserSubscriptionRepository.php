<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Subscriptions\SubscriptionPlans;
use DateTimeImmutable;
use DateTimeZone;
use PDO;

final class UserSubscriptionRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function hasActiveSubscription(int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM user_subscriptions
             WHERE user_id = ? AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1'
        );
        $stmt->execute([$userId]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * @return array{plan:string,expires_at:?string}|null expires_at formatted Y-m-d H:i:s server TZ
     */
    public function fetchActiveSubscription(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT plan, expires_at FROM user_subscriptions
             WHERE user_id = ? AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1'
        );
        $stmt->execute([$userId]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $exp = $row['expires_at'] ?? null;

        return [
            'plan' => (string) $row['plan'],
            'expires_at' => $exp === null ? null : (string) $exp,
        ];
    }

    /**
     * @return array{plan:string,expires_at:?string}|null Row if present (may be expired)
     */
    public function fetchRow(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT plan, expires_at FROM user_subscriptions WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $exp = $row['expires_at'] ?? null;

        return [
            'plan' => (string) $row['plan'],
            'expires_at' => $exp === null ? null : (string) $exp,
        ];
    }

    public function activateOrExtend(int $userId, string $plan): void
    {
        SubscriptionPlans::assertKnownPlan($plan);

        $timezone = self::timezone();
        $now = new DateTimeImmutable('now', $timezone);

        $existing = $this->fetchRow($userId);
        if ($existing !== null && $existing['expires_at'] === null) {
            return;
        }

        $isActive = $existing !== null
            && isset($existing['expires_at'])
            && $existing['expires_at'] !== null
            && new DateTimeImmutable($existing['expires_at'], $timezone) > $now;

        if ($plan === SubscriptionPlans::LIFETIME) {
            $this->upsert($userId, SubscriptionPlans::LIFETIME, null);

            return;
        }

        $base = $now;
        if ($isActive && isset($existing['expires_at']) && $existing['expires_at'] !== null) {
            $currentEnd = new DateTimeImmutable($existing['expires_at'], $timezone);
            if ($currentEnd > $base) {
                $base = $currentEnd;
            }
        }

        $newExpiry = match ($plan) {
            SubscriptionPlans::MONTHLY => $base->modify('+1 month'),
            SubscriptionPlans::YEARLY => $base->modify('+1 year'),
            default => throw new \InvalidArgumentException('Unsupported renew plan.'),
        };

        $formatted = $newExpiry->format('Y-m-d H:i:s');
        $this->upsert($userId, $plan, $formatted);
    }

    private function upsert(int $userId, string $plan, ?string $expiresAtMysql): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user_subscriptions (user_id, plan, expires_at, granted_at)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                  plan = VALUES(plan),
                  expires_at = VALUES(expires_at),
                  granted_at = VALUES(granted_at)'
        );

        $stmt->execute([
            $userId,
            $plan,
            $expiresAtMysql,
        ]);
    }

    /**
     * @return array{plan:string,expires_at:?string,granted_at:string}|null
     */
    public function fetchSubscriptionDetails(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT plan, expires_at, granted_at FROM user_subscriptions WHERE user_id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $exp = $row['expires_at'] ?? null;

        return [
            'plan' => (string) $row['plan'],
            'expires_at' => $exp === null ? null : (string) $exp,
            'granted_at' => (string) $row['granted_at'],
        ];
    }

    public function deleteByUserId(int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM user_subscriptions WHERE user_id = ?');
        $stmt->execute([$userId]);
    }

    private static function timezone(): DateTimeZone
    {
        $tz = date_default_timezone_get();

        try {
            return new DateTimeZone($tz ?: 'UTC');
        } catch (\Exception) {
            return new DateTimeZone('UTC');
        }
    }
}
