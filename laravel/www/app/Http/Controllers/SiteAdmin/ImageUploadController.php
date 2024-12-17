<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Models\ImageInfo;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    use APIResponse;

    public function upload(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'file_type' => 'required|in:general,featured',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        // Store the image
        $path = $request->file('image')->store('images', 'public');
        $hash = md5_file($request->file('image')->getRealPath());

        // Save image info to the database
        $imageInfo = ImageInfo::create([
            'public_url' => Storage::url($path),
            'storage_url' => $path,
            'title' => $request->input('title'),
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'hash' => $hash,
            'file_type' => $request->input('file_type'),
        ]);

        if ($imageInfo) {
            return $this->successResponseWithData($imageInfo, 'Image uploaded successfully');
        }

        return $this->failureResponse();

    }
}
