<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Jobs\OptimizeImageJob;
use App\Models\CastImage;
use App\Models\Customer\CustomerImage;
use App\Models\ImageInfo;
use App\Models\Optimization;
use App\Models\SiteAdminImage;
use App\Models\StoreImage;
use app\Services\ImageHandleService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    use APIResponse;

    protected $imageHandleService;

    public function __construct(ImageHandleService $imageHandleService)
    {
        $this->imageHandleService = $imageHandleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'title' => 'string',
            'alt_text' => 'string',
            'caption' => 'string',
            'file_type' => 'string|required',
            'user_id' => 'integer',
            'user_type' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator, 422);
        }

        $userId = $request->user_id ?? auth()->id();
        $userType = $request->user_type ?? auth()->user()->account_type;

        $successfulImages = [];
        $failedImages = [];

        foreach ($request->file('images') as $image) {
            DB::beginTransaction();

            try {
                $imageUpload = $this->handleImageUpload($image, $userType, $userId);
                if (!$imageUpload) {
                    throw new \Exception('Image upload failed');
                }

                $imageInsert = $this->saveImageInfo($request, $imageUpload);
                if (!$imageInsert) {
                    throw new \Exception('Failed to save image information');
                }

                $preOptimizationData = $this->savePreOptimizationData($imageInsert->id, $imageUpload);
                if (!$preOptimizationData) {
                    throw new \Exception('Failed to save pre-optimization data');
                }

                $this->updatePreOptimizationData($preOptimizationData, $imageUpload);
                if (!$this->associateImageWithUser($userType, $userId, $imageInsert->id)) {
                    throw new \Exception('User type not found');
                }

                $successfulImages[] = $imageInsert;

                dispatch(new OptimizeImageJob($imageInsert->id));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $failedImages[] = [
                    'image' => $image->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
                continue;
            }
        }

        if (count($failedImages) > 0) {
//            return $this->failureResponseWithMessage('Some images failed to process', $failedImages);
            return responseJson(false, 'Some images failed to process', $failedImages, 422);
        }

        return $this->successResponseWithData($successfulImages);
    }

    private function handleImageUpload($image, $user_type, $user_id)
    {
        return $this->imageHandleService->upload($image, $user_type, $user_id);
    }

    private function saveImageInfo($request, $image_upload)
    {
        return ImageInfo::create([
            'public_url' => $image_upload['public_url'],
            'storage_url' => $image_upload['storage_url'],
            'title' => $request->title ?? $image_upload['title'],
            'alt_text' => $request->alt_text ?? $request->title ?? $image_upload['title'],
            'caption' => $request->caption,
            'file_type' => $request->file_type,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    private function savePreOptimizationData($image_id, $image_upload)
    {
        return Optimization::create([
            'image_id' => $image_id,
            'original_size' => $image_upload['size'],
        ]);
    }

    private function updatePreOptimizationData($pre_optimization_data, $image_upload)
    {
        if ($image_upload['webp_size']) {
            $pre_optimization_data->update([
                'webp_size' => $image_upload['webp_size'],
                'webp_conversion' => 'done',
                'optimization_status' => 'in_progress',
            ]);
        }
    }

    //    protected function associateImageWithUser($user_type, $user_id, $image)
    //    {
    //        switch (strtolower($user_type)) {
    //            case 'admin':
    //                $image->siteAdminImages()->insert([
    //                    'site_admin_id' => $user_id,
    //                    'image_id' => $image->id,
    //                ]);
    //                break;
    //            case 'cast':
    //                $image->castImages()->insert([
    //                    'cast_id' => $user_id,
    //                    'image_id' => $image->id,
    //                ]);
    //                break;
    //            case 'store':
    //                $image->storeImages()->insert([
    //                    'store_id' => $user_id,
    //                    'image_id' => $image->id,
    //                ]);
    //                break;
    //            case 'customer':
    //                $image->customerImages()->insert([
    //                    'customer_id' => $user_id,
    //                    'image_id' => $image->id,
    //                ]);
    //                break;
    //            default:
    //                return false;
    //        }
    //        return true;
    //
    //    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    private function associateImageWithUser($user_type, $user_id, $image_id)
    {
        switch (strtolower($user_type)) {
            case 'admin':
                SiteAdminImage::create([
                    'site_admin_id' => $user_id,
                    'image_id' => $image_id,
                ]);
                break;
            case 'cast':
                CastImage::create([
                    'cast_id' => $user_id,
                    'image_id' => $image_id,
                ]);
                break;
            case 'store':
                StoreImage::create([
                    'store_id' => $user_id,
                    'image_id' => $image_id,
                ]);
                break;
            case 'customer':
                CustomerImage::create([
                    'customer_id' => $user_id,
                    'image_id' => $image_id,
                ]);
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $image = ImageInfo::find($id);
            if ($image) {
                $delete = $this->imageHandleService->remove($image->public_url);
                if (!$delete) {
                    return $this->failureResponseWithMessage('Image not found or something went wrong!');
                } else {
                    $image->delete();
                }
                DB::commit();

                return $this->successResponseWithMessage('Image deleted successfully!');
            }

            return $this->failureResponseWithMessage('Image not found!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->failureResponseWithMessage('Something went wrong!');
        }
    }

    private function imageSizeFormatter($size)
    {
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 2) . ' KB';
        } else {
            return round($size / 1048576, 2) . ' MB';
        }
    }
}
