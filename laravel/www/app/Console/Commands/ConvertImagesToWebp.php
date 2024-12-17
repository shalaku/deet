<?php

namespace App\Console\Commands;

use App\Models\Optimization;
use app\Services\ImageHandleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConvertImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-images-to-webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will find and convert all unconverted images to webp format periodically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unconvertedImages = Optimization::where('webp_conversion', '=', 'failed')
            ->with('image')
            ->get();

        foreach ($unconvertedImages as $unconvertedImage) {

            $imageHandleService = new ImageHandleService;

            try {
                $imageHandleService->localWebpConversion($unconvertedImage->image->storage_url);
            } catch (\Exception $e) {
                $unconvertedImage->webp_conversion = 'failed';
                $unconvertedImage->save();
                Log::error('Failed to convert image to webp (schedule conversion): '.$unconvertedImage->image->storage_url);

                continue;
            }
        }
    }
}
