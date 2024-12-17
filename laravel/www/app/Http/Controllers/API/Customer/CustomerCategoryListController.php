<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCategoryRequest;
use App\Models\Customer\CustomerCategoryList;
use App\Traits\APIResponse;

class CustomerCategoryListController extends Controller
{
    use APIResponse;


    public function index()
    {
        $data =  CustomerCategoryList::all();

        return $this->successResponse('Customer Category List.', $data);
    }

    public function store(CustomerCategoryRequest $request)
    {

        $castCategory = CustomerCategoryList::create($request->all());

        return $this->successResponse('Customer Category Create Successfully.', $castCategory, 201);
    }

    public function show($id)
    {
        $castCategory = CustomerCategoryList::where('id', $id)->first();

        if (empty($castCategory)) {
            return $this->errorResponse('Customer Category Not Found.', 404);
        }

        return $this->successResponse('Customer Category Details.', $castCategory);
    }

    public function update(CustomerCategoryRequest $request, $id)
    {
        $castCategory = CustomerCategoryList::where('id', $id)->first();

        if (empty($castCategory)) {
            return $this->errorResponse('Customer Category Not Found.', 404);
        }

        $castCategory->update($request->all());

        return $this->successResponse('Customer Category Update Successfully.', $castCategory);
    }

    public function destroy($id)
    {
        $castCategory = CustomerCategoryList::where('id', $id)->first();

        if (empty($castCategory)) {
            return $this->errorResponse('Customer Category Not Found.', 404);
        }
        $castCategory->delete();

        return $this->successResponse('Customer Category Delete Successfully.');
    }
}
