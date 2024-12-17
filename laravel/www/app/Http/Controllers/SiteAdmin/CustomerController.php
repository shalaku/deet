<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCategoryRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerCategoryList;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CustomerController extends Controller
{
    use APIResponse;

    public function store(CustomerRegisterRequest $request)
    {
        try {
            Customer::create($request->all());

            return responseJson(true, 'Customer registered successfully');
        } catch (\Exception $e) {
            Log::info($e->getMessage().' '.$e->getLine());

            return responseJson(false, 'Something went wrong', [], $e->getCode());
        }
    }

    public function customerSearch(Request $request)
    {

        $customers = Customer::query();

        if (! empty($request->name_ja)) {
            $customers->where('name_ja', 'like', '%'.$request->name_ja.'%');
        }

        if (! empty($request->name_kana)) {
            $customers->where('name_kana', 'like', '%'.$request->name_kana.'%');
        }

        if (! empty($request->birthday)) {
            $customers->where('birthday', '=', $request->birthday);
        }

        if (! empty($request->introducer_id)) {
            $customers->where('introducer_id', '=', $request->introducer_id);
        }

        if (! empty($request->person_in_charge_id)) {
            $customers->where('person_in_charge_id', '=', $request->person_in_charge_id);
        }

        if (! empty($request->category_id_)) {
            $customers->where('category_id_', '=', $request->category_id_);
        }

        if (! empty($request->registered_date)) {
            $customers->where('registered_date', '=', $request->registered_date);
        }

        if (! empty($request->tag_of_preference)) {
            $customers->where('tag_of_preference', '=', $request->tag_of_preference);
        }

        if ($customers) {
            $customers = $customers->paginate(10);

            return responseJson(true, $customers);
        } else {
            return responseJson(false, 'No data found');
        }
    }

    public function registerCustomerCategory(CustomerCategoryRequest $request)
    {
        try {
            CustomerCategoryList::create($request->all());

            return responseJson(true, 'Customer category registered successfully');
        } catch (\Exception $e) {
            Log::info($e->getMessage(). ' ' . $e->getLine());
            return responseJson(false, 'Something went wrong', [], $e->getCode());
        }
    }

    public function customer_category_search($id)
    {
        $category = CustomerCategoryList::find($id);

        if ($category) {
            return responseJson(true,  $category);
        } else {
            return responseJson(false, 'No data found');
        }
    }

    public function customer_category_update(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'id' => 'required|exists:customer_category_lists,id',
            'category_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $category = CustomerCategoryList::where('id', $request->id)->update([
            'category_name' => $request->category_name
        ]);

        if ($category) {
            return responseJson(true,  'Customer category update successfully');
        } else {
            return responseJson(false, 'Category update fail');
        }
    }

    public function customer_category_delete($id)
    {
        $validator = validator()->make(['id' => $id], [
            'id' => 'required|integer|exists:customer_category_lists,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        if (Customer::where('category_id', $id)->exists()) {
            return responseJson(false, 'Category delete fail. This category use another table');
        } else {
            $category = CustomerCategoryList::find($id)->delete();

            if($category){
                return responseJson(true, 'Category delete successfully');
            }else{
                return responseJson(false, 'Something went wrong');
            }
        }
    }
}
