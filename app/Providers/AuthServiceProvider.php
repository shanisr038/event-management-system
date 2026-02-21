<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventTicket;
use App\Policies\CategoryPolicy;
use App\Policies\EventPolicy;
use App\Policies\RegistrationPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        EventRegistration::class => RegistrationPolicy::class,
        EventTicket::class => TicketPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }

}