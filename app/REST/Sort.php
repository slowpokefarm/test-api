<?php

namespace App\REST;

class Sort
{
    public $key;
    public $direction;

    public function __construct($key, $direction)
    {
        $this->key = $key;
        $this->direction = $direction;
    }
}
