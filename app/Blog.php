<?php

namespace App;

use App\Folksonomy\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $title
 * @property string $body
 * @property int $author_id
 * @property int $views_counter
 *
 * @property-read null|User $author
 * @property-read Collection|Tag[] $tags
 * @property-read Collection|File[] $files
 */
class Blog extends Model
{
    protected $table = 'blog';

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tags', 'blog_id', 'tag_id');
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'blog_files', 'blog_id', 'file_id');
    }
}
