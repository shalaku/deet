<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait FileUpload
{
    protected $disk = 's3';

    protected function upload($file, $directoryPath)
    {
        try {
            $path = $file->storeAs(
                $directoryPath,
                uniqid('', true) . '.' . $file->extension(),
                $this->disk
            );

            return $path;

        } catch (\Throwable $th) {
            Log::error("Image Upload Error: ", (array)$th->getMessage());
            return false;
        }
    }

    protected function delete($filePath)
    {
        Storage::disk('s3')->delete($filePath);
    }

}
