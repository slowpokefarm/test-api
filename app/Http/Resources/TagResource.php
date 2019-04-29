<?php

namespace App\Http\Resources;

use App\Blog;
use App\Folksonomy\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * @var Tag
     */
    public $resource;

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'term' => $this->resource->term
        ];
    }
}
