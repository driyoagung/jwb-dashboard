<?php

namespace App\Providers;

use App\Models\PosNotification;
use App\Models\ReferenceRecord;
use App\Models\User;
use App\Policies\ReferenceRecordPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ReferenceRecord::class, ReferenceRecordPolicy::class);
        Gate::define('pos.admin', fn (User $user): bool => $user->isAdmin());
        Gate::define('pos.sell', fn (User $user): bool => $user->is_active && in_array($user->role, ['admin', 'cashier'], true));
        View::composer('components.admin.header', function ($view): void {
            $userId = auth()->id();
            $view->with('recentNotifications', $userId
                ? PosNotification::query()->where('user_id', $userId)->latest('id')->limit(5)->get()
                : collect());
            $view->with('unreadNotificationCount', $userId
                ? PosNotification::query()->where('user_id', $userId)->whereNull('read_at')->count()
                : 0);
        });
    }
}
