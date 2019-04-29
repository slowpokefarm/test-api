<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use App\File;
use App\LinkedFile;

trait InteractsWithFiles
{
    public function linkFile(File $file, Filesystem $disk)
    {
        return new LinkedFile($file, $disk);
    }
}
