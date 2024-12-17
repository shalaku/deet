<?php

namespace app\Services;

use App\Models\ImageInfo;
use App\Models\ImageSizes;
use Davidcb\LaravelShortPixel\LaravelShortPixel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;

class ImageHandleService
{
    protected $image_variations;

    protected $shortPixel;

    /**
     * @param  $image_variations
     */
    public function __construct()
    {
        $this->image_variations = ImageSizes::all();
        $this->shortPixel = new LaravelShortPixel;
    }

    /**
     * @param  $image_variations
     */
    public function upload(UploadedFile $image, $user_type, $user_id, $is_avatar = false)
    {
        $hash = uniqid();
        $filename = $hash.'-'.strtolower($image->getClientOriginalName());
        $size = $image->getSize();
        $base_path = 'public/img/'.$user_type.'/'.$user_id;
        $destination_path = $base_path.($is_avatar ? '/avatar/' : '/original/');

        if (! Storage::exists($destination_path)) {
            Storage::makeDirectory($destination_path, 0775, true);
        }

        $title = $this->generateTitle($image->getClientOriginalName());
        $image_name = $this->sanitizeFilename($filename);

        $file_path = $destination_path.$image_name;

        if ($this->isFileExists($file_path)) {
            $image_name = $this->generateUniqueFilename($image_name, $image->getClientOriginalExtension());
        }

        $public_link = $this->generatePublicLink($destination_path, $image_name);
        $image->storeAs($destination_path, $image_name);

        $webp = $this->localWebpConversion($destination_path.$image_name);
        $webp_size = $webp ? $webp['size'] : null;
        $webp_url = $this->generateWebpUrl($public_link);
        $webp_size = filesize($webp_url);

        $this->createVariations($destination_path.'/'.$image_name);

        return $this->generateResponse($public_link, $destination_path, $image_name, $title, $size, $webp_size);
    }

    private function generateTitle($originalName)
    {
        $img_arr = explode('.', $originalName);
        array_pop($img_arr);

        return implode(' ', $img_arr);
    }

    private function sanitizeFilename($filename)
    {
        return strtolower(str_replace(' ', '-', $filename));
    }

    private function isFileExists($file_path)
    {
        return count(ImageInfo::where('storage_url', $file_path)->get()) > 0;
    }

    private function generateUniqueFilename($filename, $extension)
    {
        $temp_image_name = explode('.', $filename);
        array_pop($temp_image_name);
        $image_name_without_extension = implode('-', $temp_image_name);

        return strtolower(str_replace(' ', '-', $image_name_without_extension)).'-'.uniqid().'.'.$extension;
    }

    private function generatePublicLink($destination_path, $image_name)
    {
        return str_replace('public/img', 'storage/img', $destination_path).$image_name;
    }

    public function localWebpConversion($imagePath, $quality = 50)
    {
        $imagePath = str_replace('public/img/', 'storage/img/', $imagePath);
        $imagick = new Imagick;
        $imagick->readImage($imagePath);
        $imagick->setImageFormat('webp');
        $imagick->setCompressionQuality($quality);
        $imagePath = explode('.', $imagePath);
        array_pop($imagePath);
        $imagePath = implode('.', $imagePath);
        $webp_path = $imagePath.'.webp';
        $imagick->writeImage($webp_path);
        $imagick->clear();
        $imagick->destroy();

        $size = filesize($webp_path);

        return [
            'webp_url' => $webp_path,
            'size' => $size,
        ];
    }

    private function generateWebpUrl($public_link)
    {
        $webp_url = explode('.', $public_link);

        return $webp_url[0].'.webp';
    }

    public function createVariations($imagePath, $quality = 100)
    {
        $sizes = $this->image_variations;
        $variation = new Imagick;
        $variation->readImage(str_replace('public/img/', 'storage/img/', $imagePath));

        foreach ($sizes as $size) {
            $variation->cropThumbnailImage($size->width, $size->height);

            // Get the base directory and filename
            $variationPath = str_replace('/original/', '/'.$size->variation_name.'/', $imagePath);
            $imgArr = explode('/', $variationPath);
            $filename = array_pop($imgArr);
            $variationDir = implode('/', $imgArr);
            $variationDir = strtolower($variationDir);

            // Make sure the directory exists
            if (! Storage::exists($variationDir)) {
                Storage::makeDirectory($variationDir, 0775, true);
            }

            // Construct the full path including the filename
            $variationFullPath = $variationDir.'/'.$filename;

            $variation->setCompression(Imagick::COMPRESSION_JPEG);
            $variation->setCompressionQuality($quality);

            try {
                $variation->writeImage(str_replace('public/img/', 'storage/img/', $variationFullPath));
                $this->localWebpConversion(str_replace('public/img/', 'storage/img/', $variationFullPath));
            } catch (ImagickException $e) {
                Log::error('Error writing image: '.$e->getMessage());
            }
        }

        $variation->clear();
        $variation->destroy();
    }

    private function generateResponse($public_link, $destination_path, $image_name, $title, $size, $webp_size)
    {
        return [
            'public_url' => $public_link,
            'storage_url' => $destination_path.$image_name,
            'title' => str_replace('-', ' ', ucfirst($title)),
            'size' => $size,
            'webp_size' => $webp_size,
        ];
    }

    public function replace($imagePath, UploadedFile $image)
    {
        try {

        } catch (\Exception $e) {
            Log::error('Error replacing image: '.$e->getMessage());

            return false;
        }
    }

    public function remove($imagePath)
    {
        try {
            $original_deleted = $this->delete($imagePath);
            if ($original_deleted) {
                $this->deleteVariations($imagePath);

                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error removing image: '.$e->getMessage());

            return false;
        }

    }

    private function delete($imagePath)
    {
        try {
            $webp_path = $this->getWebpPath($imagePath);
            Storage::delete($webp_path);
            Storage::delete($imagePath);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting image: '.$e->getMessage());

            return false;
        }
    }

    protected function getWebpPath($imagePath)
    {
        $imagePath = explode('.', $imagePath);
        array_pop($imagePath);

        return implode('.', $imagePath).'.webp';
    }

    private function deleteVariations($imagePath)
    {
        try {
            foreach ($this->image_variations as $size) {
                $variationPath = str_replace(['/original/', '/avatar/'], '/'.$size->variation_name.'/', $imagePath);
                Storage::delete($variationPath);
                Storage::delete($this->getWebpPath($variationPath));
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting variations: '.$e->getMessage());

            return false;
        }
    }

    public function shortPixelImageOptimizationByID($id)
    {
        $optimizationService = new ImageOptimizationService;
        $optimizationService->optimizeImageById($id);

        return true;
    }
}
