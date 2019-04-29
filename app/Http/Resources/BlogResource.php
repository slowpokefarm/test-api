<?php

namespace App\Http\Resources;

use App\Blog;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * @var Blog
     */
    public $resource;

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'body' => $this->resource->body,
            'views_counter' => $this->resource->views_counter
        ];
    }
}
