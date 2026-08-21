<?php

namespace App\Providers;

use App\Models\MrKolomTambahan;
use App\Models\MrRisiko;
use App\Observers\MrKolomTambahanObserver;
use App\Observers\MrRisikoObserver;
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
        MrRisiko::observe(MrRisikoObserver::class);
        MrKolomTambahan::observe(MrKolomTambahanObserver::class);
    }
}
