<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Resources\FileResource;
use App\LinkedFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileController extends Controller
{
    use InteractsWithFiles;

    protected $disk;

    public function __construct(Filesystem $disk)
    {
        $this->disk = $disk;
    }

    public function upload(Request $request)
    {
        $content = $request->input('content');

        if ($this->disk->put($path = Str::random(), $content)) {
            $file = new File();
            $file->path = $path;
            $file->save();

            return new FileResource($this->linkFile($file, $this->disk));
        }

        return response()->make('Unable to upload file', 500);
    }

    // без правил
    public function delete(File $file)
    {
        if (!$this->disk->delete($file->path)) {
            return response()->make('Unable to delete file', 500);
        }
    }

    public function get(File $file)
    {
        return new FileResource($this->linkFile($file, $this->disk));
    }
}
