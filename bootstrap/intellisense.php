<?php

interface ResponseFactoryMacro
{
    /**
     * @param string $token
     * @return mixed
     */
    public function token($token);
}

/**
 * @return \Tymon\JWTAuth\JWTGuard
 */
function auth()
{

}

/**
 * @return ResponseFactoryMacro
 */
function response()
{

}
