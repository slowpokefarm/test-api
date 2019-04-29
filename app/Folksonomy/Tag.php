<?php

namespace App\Folksonomy;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $term
 */
class Tag extends Model
{
    protected $table = 'tag_catalog';

    public $timestamps = false;
}
