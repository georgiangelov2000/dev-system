<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $permissions = [
            'access-profile' => 1,
            'access-country' => 2,
            'access-role-management' => 3,
            'access-general' => 4,
            'access-product-widgets' => 5,
            'access-supplier-management' => 6,
            'access-customer-management' => 7,
            'access-purchase-management' => 8,
            'access-package-management' => 9,
            'access-orders-management' => 10,
            'access-staff-members' => 11,
            'access-payments-management' => 12,
            'access-invoices-management' => 13,
            'access-settings-management' => 14,
            'access-reports-management' => 15,
            'access-imports-management' => 16,
            'access-logs-management' => 17,
            'access-notifications-management' => 18,
        ];

        foreach ($permissions as $permission => $identity) {
            Gate::define($permission, function ($user) use ($identity) {
                return in_array($identity, $user->role->rolesAccessManagement->pluck('identity')->toArray());
            });
        }
    }
}
