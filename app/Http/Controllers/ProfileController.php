<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Auth\RequireAuthentication;
use App\Repositories\UserRepository;
use App\Repositories\UserSubscriptionRepository;
use App\Subscriptions\TodoQuota;
use PDOException;
use Throwable;

final class ProfileController
{
    public function index(): void
    {
        RequireAuthentication::redirectToLoginIfGuest('/profile');

        $sessionUser = auth_user();
        $userId = (int) $sessionUser['user_id'];

        /** @var string $noticeType */
        $noticeType = 'success';
        /** @var string $noticeMessage */
        $noticeMessage = '';

        /** @var array{id:int,name:string,email:string,created_at:string}|null $profile */
        $profile = null;
        /** @var array{plan:string,expires_at:?string,granted_at:string}|null $subscriptionDetails */
        $subscriptionDetails = null;
        /** @var array{plan:string,expires_at:?string}|null $activeSubscription */
        $activeSubscription = null;
        $freeLimit = TodoQuota::freeTodoLimit();

        try {
            $users = new UserRepository(db());
            $subs = new UserSubscriptionRepository(db());

            $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
            if ($method === 'POST') {
                $action = strtolower(trim((string) ($_POST['_action'] ?? '')));

                if ($action === 'cancel_subscription') {
                    if (!csrf_validate($_POST['_token'] ?? null)) {
                        $noticeType = 'error';
                        $noticeMessage = 'Session expired — refresh this page and try again.';
                    } elseif (!isset($_POST['confirm_cancel']) || $_POST['confirm_cancel'] !== '1') {
                        $noticeType = 'error';
                        $noticeMessage = 'Check the box to confirm you want to end subscription access.';
                    } elseif (!$subs->hasActiveSubscription($userId)) {
                        $noticeType = 'error';
                        $noticeMessage = 'You do not have an active subscription to cancel.';
                    } else {
                        $subs->deleteByUserId($userId);
                        $noticeType = 'success';
                        $noticeMessage = 'Subscription access removed. You are on the free tier again ('
                            . $freeLimit . ' todos max). Your todos were not deleted.';
                    }
                }
            }

            $profile = $users->findPublicProfileById($userId);
            $subscriptionDetails = $subs->fetchSubscriptionDetails($userId);
            $activeSubscription = $subs->fetchActiveSubscription($userId);
        } catch (PDOException $e) {
            $errno = $e->errorInfo[1] ?? null;
            $missing = ($errno === 1146)
                || str_contains($e->getMessage(), 'Base table or view not found')
                || str_contains($e->getMessage(), "doesn't exist");

            $noticeType = 'error';
            $noticeMessage = $missing
                ? 'Database tables missing. From the project root run: php database/migrate.php'
                : (((bool) ($GLOBALS['config']['debug'] ?? false))
                    ? $e->getMessage()
                    : 'We could not load your profile. Try again shortly.');
        } catch (Throwable $e) {
            if ((bool) ($GLOBALS['config']['debug'] ?? false)) {
                throw $e;
            }
            $noticeType = 'error';
            $noticeMessage = 'Something went wrong.';
        }

        require view_path('profile.view.php');
    }
}
