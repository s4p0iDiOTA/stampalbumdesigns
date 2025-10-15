<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::panel(function ($panel) {
            // Get all resources except StaffResource, and add our custom UserResource
            $resources = collect(\Lunar\Admin\LunarPanelManager::getResources())
                ->reject(fn ($resource) => $resource === \Lunar\Admin\Filament\Resources\StaffResource::class)
                ->push(\App\Filament\Resources\UserResource::class) // Add custom User resource
                ->values()
                ->toArray();

            return $panel
                ->authGuard('web')
                ->authPasswordBroker('users')
                ->resources($resources);
        })->register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
