<?php

namespace Remachinon\Providers;

use Remachinon\Models\Device;
use Remachinon\Policies\DevicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Remachinon\Models\Device' => 'Remachinon\Policies\DevicePolicy',
        'Remachinon\Models\DeviceTunnel' => 'Remachinon\Policies\DeviceTunnelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies();

        // Ejemplo de before filter para comprobar isAdmins y cosas así
        // Si esto devuelve true no se comprueba la policy siquiera...
//        $gate->before(function($user) {
//            return $user->id == 666; // por un poner
//        });
    }
}
