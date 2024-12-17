<?php

namespace App\Services;

use App\Models\ImageSizes;
use App\Models\Optimization;
use Davidcb\LaravelShortPixel\LaravelShortPixel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    protected $shortPixel;

    protected $variations;

    public function __construct()
    {
        $this->shortPixel = new LaravelShortPixel;
        $this->variations = ImageSizes::all();
    }

    public function optimizeImageById($id)
    {
        DB::beginTransaction();

        try {
            $image = Optimization::with('image')->where('image_id', $id)->first();

            if (! $image) {
                return false;
            }

            $imagePath = storage_path('app/'.$image->image->storage_url);
            $savePath = $this->getSavePath($image->image->storage_url);

            $this->optimizeAndSave($imagePath, $savePath);

            foreach ($this->variations as $variation) {
                $imageVariationPath = $this->getVariationPath($imagePath, $variation->variation_name);
                $saveVariationPath = $this->getVariationPath($savePath, $variation->variation_name);
                $this->optimizeAndSave($imageVariationPath, $saveVariationPath);
            }

            $image->update([
                'optimization_status' => 'done',
                'optimized_size' => $this->getImageSize($imagePath),
                'webp_size' => $this->getImageSize($this->getWebpURL($imagePath)),
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Image optimization failed: '.$e->getMessage());

            // Update the optimization status outside the transaction
            Optimization::where('image_id', $id)->update(['optimization_status' => 'failed']);

            return false;
        }
    }

    protected function getSavePath($storageUrl)
    {
        return storage_path('app/'.dirname($storageUrl).'/');
    }

    protected function optimizeAndSave($imagePath, $savePath)
    {
        $this->shortPixel->fromFiles($imagePath, $savePath);
    }

    protected function getVariationPath($path, $variation)
    {
        return str_replace(['original', 'avatar'], $variation, $path);
    }

    protected function getImageSize($imagePath)
    {
        return file_exists($imagePath) ? filesize($imagePath) : 0;
    }

    protected function getWebpURL($imagePath)
    {
        return preg_replace('/\.\w+$/', '.webp', $imagePath);
    }
}
