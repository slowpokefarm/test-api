<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Http\Resources\BlogResource;
use App\User;

class UserController extends Controller
{
    public function userBlogs(User $user) {
        $query = Blog::query();
        $query->whereColumn('author_id', $user->id);
        $paginator = $query->paginate();

        return BlogResource::collection($paginator);
    }
}
