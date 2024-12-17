<?php

namespace Database\Seeders;

use App\Models\ImageSizes;
use Illuminate\Database\Seeder;

class ImagesizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ImageSizes::insert([
            [
                'variation_name' => 'thumbnail',
                'height' => 150,
                'width' => 150,
                'aspect_ratio' => '1:1',
            ],
            [
                'variation_name' => 'small',
                'height' => 240,
                'width' => 320,
                'aspect_ratio' => '4:3',
            ],
            [
                'variation_name' => 'medium',
                'height' => 480,
                'width' => 640,
                'aspect_ratio' => '4:3',
            ],
            [
                'variation_name' => 'large',
                'height' => 720,
                'width' => 1280,
                'aspect_ratio' => '16:9',
            ],
            [
                'variation_name' => 'extra_large',
                'height' => 1080,
                'width' => 1920,
                'aspect_ratio' => '16:9',
            ],
        ]);
    }
}
