<?php

namespace App\Http\Controllers\API\Cast;

use App\Http\Controllers\Controller;
use App\Http\Requests\CastCategoryRequest;
use App\Models\Cast\CastCategory;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class CastCategoryController extends Controller
{

    use APIResponse;


    public function index()
    {
        $data =  CastCategory::all();

        return $this->successResponse('Cast Category List.', $data);
    }

    public function store(CastCategoryRequest $request)
    {

        $castCategory = CastCategory::create($request->all());

        return $this->successResponse('Cast Category Create Successfully.', $castCategory, 201);
    }

    public function show($id)
    {
        $castCategory = CastCategory::where('id', $id)->first();

        if (empty($castCategory)) {
            return $this->errorResponse('Cast Category Not Found.', 404);
        }

        return $this->successResponse('Cast Category Details.', $castCategory);
    }

    public function update(CastCategoryRequest $request, $id)
    {
        $castCategory = CastCategory::where('id', $id)->first();

        if (empty($castCategory)) {
            return $this->errorResponse('Cast Category Not Found.', 404);
        }

        $castCategory->update($request->all());

        return $this->successResponse('Cast Category Update Successfully.', $castCategory);
    }

    public function destroy($id)
    {
        $castCategory = CastCategory::where('id', $id)->first();

        if (empty($castCategory)) {
            return $this->errorResponse('Cast Category Not Found.', 404);
        }
        $castCategory->delete();

        return $this->successResponse('Cast Category Delete Successfully.');
    }
}
