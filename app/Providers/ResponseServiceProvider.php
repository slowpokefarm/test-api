<?php

namespace App\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ResponseServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        ResponseFactory::macro('token', function ($token) {
            return $token;
        });
    }
}
