<?php

namespace App\Http\Controllers\Blog;

use App\Blog;
use App\Folksonomy\Folksonomy;
use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    protected $folksonomy;

    public function __construct(Folksonomy $folksonomy)
    {
        $this->folksonomy = $folksonomy;
    }

    public function list(Blog $blog)
    {
        return TagResource::collection($blog->tags);
    }

    public function attach(Blog $blog, Request $request)
    {
        $this->validate($request, ['term' => 'required']);
        $blog->tags()->attach($this->folksonomy->findOrCreate(trim($request->input('term'))));

        return response()->noContent();
    }

    public function detach(Blog $blog, Request $request)
    {
        $this->validate($request, ['term' => 'required']);
        $blog->tags()->detach($this->folksonomy->findOrCreate(trim($request->input('term'))));

        return response()->noContent();
    }

    public function get(Blog $blog)
    {
        return TagResource::collection($blog->tags);
    }
}
