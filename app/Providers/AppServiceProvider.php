<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Menu;
use App\Models\User;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());

        View::composer('*', fn($view) => 
            $view->with('menus', Menu::whereNull('parent_id')->where('is_active', true)->with('children')->orderBy('sort_order')->get()
            )
        );
    }
}
