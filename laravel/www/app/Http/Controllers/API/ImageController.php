<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MakeProfilePictureRequest;
use App\Models\Cast\CastImage;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerImage;
use App\Models\Image;
use App\Traits\APIResponse;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    use APIResponse;
    use FileUpload;
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $image = Image::where('id', $id)->first();

            if (empty($image)) {
                return $this->errorResponse('Image Not Found.', 404);
            }

            if ($image->image_for == 'cast') {
                CastImage::where('image_id', $image->id)->delete();
            }

            $image->delete();
            $this->delete($image->image_path);

            DB::commit();

            return $this->successResponse('Image Delete Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
             return $this->errorResponse('Image Delete Failed', 500, ['error' => $th->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        $images = $request['images'];

        return $this->uploadImage($images);

    }

    public function uploadImage($images)
    {
        DB::beginTransaction();
        try {
            foreach ($images as $image) {

                if (!in_array($image['image_for'], ['cast', 'customer']))
                {
                    continue;
                }
                $owner_id = $image['owner_id'];
                $path = $this->upload($image['image'], $image['image_for']);

                if (!$path) {
                    continue;
                }

                $data = [
                    'image_path' => $path,
                    'image_for' => $image['image_for'],
                    'file_type' => $image['file_type'] ?? null,
                ];
                $imageData = Image::create($data);

                if ($image['image_for'] == 'cast') {

                    CastImage::create(['image_id' => $imageData->id, 'cast_id' => $owner_id]);
                } elseif ($image['image_for'] == 'customer') {

                    CustomerImage::create(['image_id' => $imageData->id, 'customer_id' => $owner_id]);
                }
            }

            DB::commit();

            return $this->successResponse('Image Add Successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->errorResponse('Image Upload Failed', 500, ['error' => $e->getMessage()]);
        }
    }

    public function makeProfilePicture(MakeProfilePictureRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            if ($data['image_for'] == 'cast') {

                $images = CastImage::where('cast_id', $data['owner_id'])->pluck('image_id')->toArray();
            }

            Image::whereIn('id', $images)->update(['is_profile_picture' => 0]);
            Image::where('id', $data['image_id'])->update(['is_profile_picture' => 1]);

            DB::commit();
            return $this->successResponse('Image Make Profile Picture Successfully.');

        } catch (\Throwable $th) {

            DB::rollBack();
            return $this->errorResponse('Failed', 500, ['error' => $th->getMessage()]);
        }
    }
}
