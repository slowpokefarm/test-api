<?php

namespace App\Providers;

use App\Blog;
use App\User;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
     * @param Gate $gate
     * @return void
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies();

        $gate->define('blog-create', function (User $user) {
            return true;
        });

        $gate->define('blog-update', function (User $user, Blog $record) {
            return $user->id == $record->author_id;
        });
    }
}
