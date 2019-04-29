<?php

namespace Tests;

use App\User;
use Faker\Generator as Faker;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @mixin TestCase
 */
trait InteractsWithUser
{
    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return User|mixed
     */
    protected function registerUser($username, $password, $email)
    {
        if (auth()->user()) {
            auth()->logout();
        }

        try {
            $this->json('POST', '/api/auth/register', [
                'name' => $username,
                'password' => $password,
                'password_confirmation' => $password,
                'email' => $email
            ]);

            auth()->logout();
        } catch (JWTException $ex) {
            // Ошибка при разлогировании незалогиненного, не до конца вообще понятно как работает система авторизации в тестах
        }

        return User::query()->where(['name' => $username])->first();
    }

    protected function registerRandomUser(Faker $faker)
    {
        return factory(User::class)->create();
        return $this->registerUser($faker->userName, $faker->password, $faker->email);
    }

    protected function actingAsScoped($user, $scopedClosure, $driver = null)
    {
        /** @var AuthManager $auth */
        $auth = $this->app['auth'];
        $prev = $auth->guard($driver)->user();

        $this->actingAs($user, $driver);
        $scopedClosure();
        $this->actingAs($prev, $driver);
    }
}
