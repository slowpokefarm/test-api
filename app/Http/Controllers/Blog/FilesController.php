<?php

namespace App\Http\Controllers\Blog;

use App\Blog;
use App\File;
use App\Http\Controllers\Controller;
use App\Http\Controllers\InteractsWithFiles;
use App\Http\Resources\FileResource;
use App\LinkedFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    use InteractsWithFiles;

    protected $disk;

    public function __construct(Filesystem $disk)
    {
        $this->disk = $disk;
    }

    public function list(Blog $blog)
    {
        return FileResource::collection($blog->files);
    }

    public function attach(Blog $blog, Request $request)
    {
        $blog->files()->attach($this->getFilesFromRequest($request));

        return response()->noContent();
    }

    public function detach(Blog $blog, Request $request)
    {
        $blog->files()->detach($this->getFilesFromRequest($request));

        return response()->noContent();
    }

    public function get(Blog $blog)
    {
        return FileResource::collection($blog->files->map(function (File $file) {
            return $this->linkFile($file, $this->disk);
        }));
    }

    protected function getFilesFromRequest(Request $request)
    {
        return File::query()->findMany(collect($request->input('file_id'))->take(10)); // Может без выборки из бд?
    }
}
