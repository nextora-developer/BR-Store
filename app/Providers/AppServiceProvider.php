<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PopupBanner;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $popupBanner = PopupBanner::where('is_active', true)->latest()->first();
            $view->with('popupBanner', $popupBanner);
        });
    }
}
