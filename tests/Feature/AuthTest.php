<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\InteractsWithUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Support\RefreshFlow;

class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use InteractsWithUser;

    /**
     * @param string $email
     * @param string $password
     * @return string token
     */
    protected function loginJWT($email, $password)
    {
        $resp = $this->json('POST', '/api/auth/login', ['email' => $email, 'password' => $password]);

        return $resp->status() === 401 ? null : $resp->content();
    }

    public function testRegister()
    {
        try {
            auth()->logout();
        } catch (JWTException $ex) {
        }

        $this->json('POST', '/api/auth/register', [
            'name' => $username = $this->faker->userName,
            'password' => $password = $this->faker->password,
            'password_confirmation' => $password,
            'email' => $email = $this->faker->email
        ]);

        try {
            auth()->logout();
        } catch (JWTException $ex) {
        }

        $this->assertDatabaseHas('users', ['name' => $username, 'email' => $email]);
    }

    // Иногда не срабатывает. Стоит запускать с флагом --process-isolation
    public function testLogin()
    {
        $this->registerUser($this->faker->userName, $password = $this->faker->password, $email = $this->faker->email);

        $this->assertEquals(null, $this->loginJWT($email, 'invalid password'));
        $this->assertGuest();

        $this->assertNotEquals(null, $this->loginJWT($email, $password));
        $this->assertAuthenticated();

        $this->json('POST', '/api/auth/logout')->assertStatus(200);
        $this->assertGuest();
    }
}
