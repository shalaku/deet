<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastRequest;
use App\Models\Cast;
use App\Models\CastCategoryList;
use App\Models\CastSpecTagList;
use App\Models\Status;
use App\Models\UserList;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CastController extends Controller
{
    use APIResponse;

    public function registerSpecTag(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'spec_tag_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $specTagRegistration = CastSpecTagList::create([
            'spec_tag_name' => $request->input('spec_tag_name'),
        ]);

        if ($specTagRegistration) {
            return $this->successResponseWithMessage('Cast spec tag registered successfully');
        }

        return $this->failureResponse();
    }

    public function castList()
    {
        $castList = Cast::all();

        if ($castList->isNotEmpty()) {
            return $this->successResponseWithData($castList);
        }

        return $this->failureResponse();
    }

    public function castSpecTagList()
    {
        $castSpecTagList = CastSpecTagList::all();

        if ($castSpecTagList->isNotEmpty()) {
            return $this->successResponseWithData($castSpecTagList);
        }

        return $this->failureResponse();
    }

    public function castSpecTagUpdate(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'spec_tag_name' => 'required|string',
            'spec_tag_id' => 'required|integer|exists:cast_spec_tag_lists,id',
        ]);

        if ($validator->fails()) {
            return $this->successResponseWithData($validator);
        }

        $updateSpecTag = CastSpecTagList::where('id', $request->input('spec_tag_id'))->update([
            'spec_tag_name' => $request->input('spec_tag_name'),
        ]);
        if ($updateSpecTag) {
            return $this->successResponse();
        }

        return $this->failureResponse();
    }

    public function castSpecTagDelete(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'spec_tag_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $deleteSpecTag = CastSpecTagList::where('id', $request->input('spec_tag_id'))->delete();
        if ($deleteSpecTag) {
            return $this->successResponse();
        }

        return $this->failureResponse();
    }

    public function getSingleSpecTag($id)
    {
        $castSpecTag = CastSpecTagList::where('id', $id)->first();

        if ($castSpecTag) {
            return $this->successResponseWithData($castSpecTag);
        }

        return $this->failureResponse();
    }

    public function registerCastCategory(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'category_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $new_category = CastCategoryList::create([
            'category_name' => $request->input('category_name'),
        ]);

        if ($new_category) {
            return $this->successResponseWithMessage('Cast category registered successfully');
        }

        return $this->failureResponse();
    }

    public function castCategoryList()
    {
        $castCategoryList = CastCategoryList::all();
        if ($castCategoryList->isNotEmpty()) {
            return $this->successResponseWithData($castCategoryList);
        }

        return $this->failureResponse();
    }

    public function statusList()
    {
        $statusList = Status::all();
        if ($statusList->isNotEmpty()) {
            return $this->successResponseWithData($statusList);
        }

        return $this->failureResponse();
    }

    public function castCategoryUpdate(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'category_name' => 'required|string',
            'category_id' => 'required|integer|exists:cast_category_lists,id',
        ]);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $updateCastCategory = CastCategoryList::where('id', $request->input('category_id'))->update([
            'category_name' => $request->input('category_name'),
        ]);

        if ($updateCastCategory) {
            return $this->successResponseWithMessage('Cast category updated successfully');
        }

        return $this->failureResponse();

    }

    public function castCategoryDelete(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'category_id' => 'required|integer||exists:cast_category_lists,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $deleteCastCategory = CastCategoryList::where('id', $request->input('category_id'))->delete();

        if ($deleteCastCategory) {
            return $this->successResponseWithMessage('Cast category deleted successfully');
        }

        return $this->failureResponse();
    }

    public function getSingleCastCategory($id)
    {
        $castCategory = CastCategoryList::where('id', $id)->first();

        if ($castCategory) {
            return $this->successResponseWithData($castCategory);
        }

        return $this->failureResponse();
    }

    public function store(StoreCastRequest $request)
    {
        $request_data = $request->only(
            'user_id', 
            'name_ja', 
            'name_kana', 
            'city', 
            'introducer_id', 
            'person_in_charge_id', 
            'registered_date', 
            'status_id', 
            'rank', 
            'post_code', 
            'prefecture', 
            'municipalitie', 
            'street', 
            'building', 
            'station', 
            'footwork', 
            'alcohol', 
            'day_work', 
            'night_work', 
            'height', 
            'three_size_b', 
            'three_size_w', 
            'three_size_h', 
            'vip_status', 
            'birthday', 
            'ceo_check', 
            'instagram_id', 
            'category_id', 
            'tag_of_spec', 
            'notices', 
            'email', 
            'password',
            'phone_number',
            'line_id',
            'basic_point_price'
        );

        try {
            DB::beginTransaction();

            $user_obj = UserList::create([
                'name_ja' => $request_data['name_ja'],
                'email' => $request_data['email'],
                'password' => Hash::make($request_data['password']),
                'status' => 'active',
                'account_type' => 'cast',
                'user_type' => '-',
            ]);

            (new Cast)->createNew($request_data + ['user_id' => $user_obj->id]);

            DB::commit();

            return responseJson(true, 'Cast created successfully');
        } catch (\Exception $e) {
            DB::rollback();

            Log::info($e->getMessage().' '.$e->getLine());

            return responseJson(false, 'Something went wrong', [], $e->getCode());
        }
    }

    public function castSearch(Request $request)
    {
        $castList = Cast::query();

        if (! empty($request->name_ja)) {
            $castList->where('name_ja', 'like', '%'.$request->name_ja.'%');
        }

        if (! empty($request->name_kana)) {
            $castList->where('name_kana', 'like', '%'.$request->name_kana.'%');
        }

        if (! empty($request->birthday)) {
            $castList->where('birthday', '=', $request->birthday);
        }

        if (! empty($request->introducer_id)) {
            $castList->where('introducer_id', '=', $request->introducer_id);
        }

        if (! empty($request->person_in_charge_id)) {
            $castList->where('person_in_charge_id', '=', $request->person_in_charge_id);
        }

        if (! empty($request->registered_date)) {
            $castList->where('registered_date', '=', $request->registered_date);
        }

        if ($castList) {
            $castList = $castList->paginate(10);

            return responseJson(true, $castList);
        } else {
            return responseJson(false, 'No data found');
        }
    }

    public function castReferralSearch(Request $request){
        $request->validate([
            'search' => 'string|max:255',
            'limit' => 'integer|min:1',
            'offset' => 'integer|min:0',
        ]);

        $searchParam = $request->query("search", null);
        $limit = $request->query("limit", 10);
        $offset = $request->query("offset", 0);

        $referralSearchResult = Cast::when($searchParam, fn($query) => $query->where('name_ja', 'like', $searchParam . '%'))
                ->select('id', 'name_ja')
                ->limit($limit)
                ->offset($offset)
                ->get();

        if ($referralSearchResult->isEmpty()) {
            return response()->noContent();
        }

        return $this->successResponseWithData($referralSearchResult, "Cast Referral Fetched Successfully");
    }
}
