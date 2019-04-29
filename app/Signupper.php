<?php

namespace App;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\ConnectionInterface;

class Signupper
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @param ConnectionInterface $connection
     * @param Hasher $hasher
     */
    public function __construct(ConnectionInterface $connection, Hasher $hasher)
    {
        $this->connection = $connection;
        $this->hasher = $hasher;
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $password
     * @return User
     */
    public function signup($email, $name, $password)
    {
        return $this->connection->transaction(function () use ($email, $name, $password) {
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->password = $this->hasher->make($password);
            $user->saveOrFail();

            return $user;
        });
    }
}
