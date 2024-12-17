<?php

namespace App\Http\Controllers\API\Cast;

use App\Http\Controllers\Controller;
use App\Http\Requests\CastTagRequest;
use App\Models\Cast\CastSpecTagList;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class CastTagCategoryController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $limit = $request->get('limit', 100);
        $page = $request->get('page', 1);

        $query = CastSpecTagList::query();

        if (!empty($request->spec_tag_name)) {
            $query->where('spec_tag_name', 'like', '%'.$request->spec_tag_name.'%');
        }

        $tags = $query->paginate($limit, ['*'], 'page', $page);

        $response = [
            'casts' => $tags->items(),
            'total' => $tags->total(),
            'pages' => $tags->lastPage(),
        ];
        return $this->successResponse('Casts retrieved successfully.', $response);
    }

    public function store(CastTagRequest $request)
    {
        try {
            $data = $request->all();
            $specTagRegistration = CastSpecTagList::create([
                'spec_tag_name' => $data['spec_tag_name'],
            ]);

            return $this->successResponse('Cast tag added successfully.', $specTagRegistration);
        } catch (\Throwable $th) {
            return $this->errorResponse('Cast tag create failed.', 500,  ['error' => $th->getMessage()]);
        }
    }

    public function update(CastTagRequest $request, $tagId)
    {
        try {
            $data = $request->all();
            $updateSpecTag = CastSpecTagList::where('id', $tagId)->update([
                'spec_tag_name' => $data['spec_tag_name'],
            ]);

            return $this->successResponse('Cast tag updated successfully.', $updateSpecTag);
        } catch (\Throwable $th) {
            return $this->errorResponse('Cast tag update failed.', 500,  ['error' => $th->getMessage()]);
        }
    }

    public function delete(Request $request, $tagId)
    {
        try {
            $deleteSpecTag = CastSpecTagList::where('id', $tagId)->delete();
            return $this->successResponse('Cast tag deleted successfully.', $deleteSpecTag);
        } catch (\Throwable $th) {
            return $this->errorResponse('Cast tag delete failed.', 500,  ['error' => $th->getMessage()]);
        }
    }

    public function getTag($id)
    {
        try {
            $castSpecTag = CastSpecTagList::where('id', $id)->first();
            if (!$castSpecTag) {
                return $this->errorResponse('Cast tag not found', 404);
            }
            return $this->successResponse('Cast tag retrieved successfully.', $castSpecTag);
        } catch (\Throwable $th) {
            return $this->errorResponse('Cast tag not found', 500,  ['error' => $th->getMessage()]);
        }
    }
}
