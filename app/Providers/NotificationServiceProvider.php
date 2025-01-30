<?php

namespace App\Providers;

use App\Models\Monitoring_approval;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.main', function ($view) {
            if (Auth::check()) {
                $notifications = Notification::where('user_id', Auth::id())
                    ->where('created_at', '>', Carbon::now()->subDay())
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();

                $notificationsCount = Notification::where('user_id', Auth::id())
                    ->where('created_at', '>', Carbon::now()->subDay())
                    ->count();

                $expiredApprovals = Monitoring_approval::where('approval_date', '<', Carbon::now()->subYear())->count();

                $view->with([
                    'notifications' => $notifications,
                    'notificationsCount' => $notificationsCount,
                    'monitoringCount' => $expiredApprovals,
                ]);
            }
        });
    }
}
