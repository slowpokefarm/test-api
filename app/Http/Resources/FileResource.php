<?php

namespace App\Http\Resources;

use App\Blog;
use App\File;
use App\Folksonomy\Tag;
use App\LinkedFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * @var LinkedFile
     */
    public $resource;

    protected $disk;

    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->file->id,
            'path' => $this->resource->filesystem->url($this->resource->file->path)
        ];
    }
}
