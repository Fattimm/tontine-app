<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Tontine::class    => \App\Policies\TontinePolicy::class,
        \App\Models\Membre::class     => \App\Policies\MembrePolicy::class,
        \App\Models\Cotisation::class => \App\Policies\CotisationPolicy::class,
        \App\Models\Tour::class       => \App\Policies\TourPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Paginator::useBootstrapFive();
    }
}