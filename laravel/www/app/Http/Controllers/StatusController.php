<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Models\Status;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    use APIResponse;


    public function index(Request $request)
    {
        $statusCategory = $request->get('status_category', "");

        if (empty($statusCategory)) {
            $data =  Status::all()->groupBy('status_category');

        } else {
            $data[$statusCategory] = Status::where('status_category', $statusCategory)->get();
        }
        return $this->successResponse('Status List.', $data);
    }

    public function store(StatusRequest $request)
    {
        try {
            if (Status::where('id', $request->id)->first()) {
                return $this->errorResponse('Status Id already exists.', 400);
            }
            $status = Status::create($request->all());

            return $this->successResponse('Status Create Successfully.', $status, 201);
        } catch (\Throwable $th) {
            return $this->errorResponse('Status Create Successfully.', 500, ['error' => $th->getMessage()]);
        }

    }

    public function show($id)
    {
        $status = Status::where('id', $id)->first();

        if (empty($status)) {
            return $this->errorResponse('Status Not Found.', 404);
        }

        return $this->successResponse('Status Details.', $status);
    }

    public function update(StatusRequest $request, $id)
    {
        $status = Status::where('id', $id)->first();

        if (empty($status)) {
            return $this->errorResponse('Status Not Found.', 404);
        }

        if ($request->id != $id && Status::where('id', $request->id)->first())
        {
            return $this->errorResponse('Invalid Id already exists.', 400);
        }

        $status->update($request->all());

        return $this->successResponse('Status Update Successfully.', $status);
    }

    public function destroy($id)
    {
        $status = Status::where('id', $id)->first();

        if (empty($status)) {
            return $this->errorResponse('Status Not Found.', 404);
        }
        $status->delete();

        return $this->successResponse('Status Delete Successfully.');
    }
}
