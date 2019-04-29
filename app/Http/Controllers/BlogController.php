<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Http\Resources\BlogResource;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class BlogController extends Controller
{
    public function list(Request $request)
    {
        $rest = new \App\REST\Request($request);
        $query = Blog::query();

        if ($sort = $rest->sort('created_at')) {
            $query->orderBy($sort->key, $sort->direction);
        }

        if ($rest->query('has_file')) {
            $query->whereHas('files');
        }

        if ($term = $rest->query('tag')) {
            $query->whereHas('tags', function (Builder $builder) use ($term) {
                $builder->where('term', '=', $term);
            });
        }

        return BlogResource::collection($query->paginate());
    }

    public function create(Request $request)
    {
        $input = $this->validateInput($request);

        $blog = Blog::unguarded(function () use ($input) {
            $blog = new Blog($input);
            $blog->author()->associate(auth()->user());
            $blog->save();

            return $blog;
        });

        return new BlogResource($blog);
    }

    public function update(Blog $blog, Request $request)
    {
        $input = $this->validateInput($request, true);

        Blog::unguarded(function () use ($input, $blog) {
            $blog->fill($input);
            $blog->save();
        });

        return new BlogResource($blog);
    }

    public function get(Blog $blog)
    {
        return new BlogResource($blog);
    }

    public function view(Blog $blog, Session $session)
    {
        if (!$session->get($key = "blog-view-{$blog->id}")) {
            if (Blog::query()->increment('views_counter')) {
                $session->put($key, true);
            }
        }
    }

    protected function validateInput(Request $request, $forUpdate = false)
    {
        $rules = [];

        if (!$forUpdate) {
            $rules['title'] = 'required|string';
            $rules['body'] = 'required|string';
        } else {
            $rules['title'] = 'string';
            $rules['body'] = 'string';
        }

        return $this->validate($request, $rules);
    }
}
