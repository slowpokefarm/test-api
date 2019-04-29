<?php

namespace App;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Filesystem;

class LinkedFile
{
    public $file;

    /**
     * @var Filesystem|Cloud
     */
    public $filesystem;

    public function __construct(File $file, Filesystem $filesystem)
    {
        $this->file = $file;
        $this->filesystem = $filesystem;
    }
}
