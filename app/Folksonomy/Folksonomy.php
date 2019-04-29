<?php

namespace App\Folksonomy;

class Folksonomy
{
    /**
     * @param $term
     * @return Tag|mixed
     */
    public function findOrCreate($term)
    {
        return Tag::unguarded(function () use ($term) {
            return Tag::query()->firstOrCreate(['term' => $term]);
        });
    }
}
