<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		// manipulate the default string length of database columns
		// this is required when using MySQL older than 5.7.7 or MariaDB older than 10.2.2
		// https://laravel.com/docs/5.4/migrations (Index Lengths & MySQL / MariaDB)
		\Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
