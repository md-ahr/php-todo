<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Auth\RequireAuthentication;
use App\Repositories\UserSubscriptionRepository;
use App\Subscriptions\SubscriptionPlans;
use App\Subscriptions\TodoQuota;
use PDOException;
use Throwable;

final class SubscribeController
{
    public function index(): void
    {
        RequireAuthentication::redirectToLoginIfGuest('/subscribe');

        $userId = (int) auth_user()['user_id'];
        /** @var string $noticeType */
        $noticeType = 'success';
        /** @var string $noticeMessage */
        $noticeMessage = '';
        $plans = SubscriptionPlans::catalog();

        try {
            $subs = new UserSubscriptionRepository(db());
            $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

            if ($method === 'POST') {
                $planRaw = strtolower(trim((string) ($_POST['plan'] ?? '')));

                if (!csrf_validate($_POST['_token'] ?? null)) {
                    $noticeType = 'error';
                    $noticeMessage = 'Session expired — refresh this page and try again.';
                } elseif (!TodoQuota::allowSimulatedCheckout()) {
                    $noticeType = 'error';
                    $noticeMessage = 'Checkout is not enabled. Configure payments (e.g. Stripe) or set APP_SIMULATE_SUBSCRIPTION_CHECKOUT=true during development.';
                } elseif ($planRaw !== '' && !SubscriptionPlans::isKnownPlan($planRaw)) {
                    $noticeType = 'error';
                    $noticeMessage = 'Please choose a valid plan.';
                } elseif ($planRaw !== '') {
                    SubscriptionPlans::assertKnownPlan($planRaw);
                    $subs->activateOrExtend($userId, $planRaw);
                    $label = SubscriptionPlans::find($planRaw)['title'];
                    $_SESSION['_subscribe_flash'] = "You are now subscribed ($label). Unlimited todos unlocked.";
                    header('Location: /subscribe?ok=1', true, 303);
                    exit;
                } else {
                    $noticeType = 'error';
                    $noticeMessage = 'Pick a subscription plan.';
                }
            }

            if (isset($_GET['ok'])) {
                $flash = $_SESSION['_subscribe_flash'] ?? '';
                unset($_SESSION['_subscribe_flash']);
                if (is_string($flash) && $flash !== '') {
                    $noticeType = 'success';
                    $noticeMessage = $flash;
                }
            }

            $active = $subs->fetchActiveSubscription($userId);
        } catch (PDOException $e) {
            $errno = $e->errorInfo[1] ?? null;
            $missing = ($errno === 1146)
                || str_contains($e->getMessage(), 'Base table or view not found')
                || str_contains($e->getMessage(), "doesn't exist");

            $noticeType = 'error';
            $noticeMessage = $missing
                ? 'Subscriptions table missing. From the project root run: php database/migrate.php'
                : (((bool) ($GLOBALS['config']['debug'] ?? false))
                    ? $e->getMessage()
                    : 'We could not reach the database. Try again shortly.');

            $active = null;
        } catch (Throwable $e) {
            if ((bool) ($GLOBALS['config']['debug'] ?? false)) {
                throw $e;
            }
            $noticeType = 'error';
            $noticeMessage = 'Something went wrong.';
            $active = null;
        }

        require view_path('subscribe.view.php');
    }
}
