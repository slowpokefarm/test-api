<?php
use \Illuminate\Database\Eloquent\Factory;
use App\Blog;

/**
 * @var Factory $factory
 */

$factory->define(Blog::class, function(\Faker\Generator $faker) {
    return [
        'title' => $faker->text,
        'body' => $faker->text,
        'author_id' => null
    ];
});
