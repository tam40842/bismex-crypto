<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Roles;
use App\Permissions;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
     public function boot()
    {
        $this->registerPolicies();

        
        /**
         * check user status = 1
         * kiểm tra filed permission != null
         * kiểm tra permission đó vs quyền có trùng vs nhau không
         * nếu user đó permission là supper-admin thì không cần check cho vào luôn
         */
        Gate::define('modules', function ($user, $module) {
            if($user->status == 1) {
                if(!is_null($user->permission)) {
                    if($user->permission == 'supper-admin') {
                        return true;
                    }
                    $role = Roles::where('slug', $user->permission)->first();
                    if(!is_null($role && $user->permission == $role)) {
                        $modules = Permissions::where('id_role', $role->id)->pluck('slug_module')->toArray();
                        if(!is_null($modules) && in_array($module, $modules)) {
                            return true;
                        }
                    }
                }
            }
            abort(404);
        });
    }
}
