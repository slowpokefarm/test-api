<?php

namespace Tests;

use App\File;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @mixin TestCase
 */
trait InteractsWithFile
{
    /**
     * @param Faker $faker
     * @return mixed|File
     */
    protected function createFile(Faker $faker)
    {
        return File::query()->find($this->json('POST', '/api/file', ['content' => $faker->text])->json('data.id'));
    }
}
