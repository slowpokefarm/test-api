<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $path
 */
class File extends Model
{
    protected $table = 'file';
    public $timestamps = false;
}
