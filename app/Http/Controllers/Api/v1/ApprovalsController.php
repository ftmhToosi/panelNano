<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ApprovalsRequest;
use App\Http\Requests\User\ApprovalsUpdateRequest;
use App\Models\Approvals;
use App\Models\Contract;
use App\Models\Estate;
use App\Models\Pledge;
use Illuminate\Http\JsonResponse;

class ApprovalsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = Approvals::with('facilities')->get();
            return response()->json(
                $data,
                200
            );
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function store(ApprovalsRequest $request): JsonResponse
    {
        try {
            $facilities_id = $request->facilities_id;

            $approvals = $request->approvals;
            foreach ($approvals as $approval){
                $approval_item = new Approvals();
                $approval_item->facilities_id = $facilities_id;
                $approval_item->license = $approval['license'];
                $approval_item->reference = $approval['reference'];
                $approval_item->date = $approval['date'];
                $approval_item->validity = $approval['validity'];
                $approval_item->description = $approval['description'];
                $approval_item->save();
            }

            $contracts = $request->contracts;
            foreach ($contracts as $contract){
                $contract_item = new Contract();
                $contract_item->facilities_id = $facilities_id;
                $contract_item->subject = $contract['subject'];
                $contract_item->name = $contract['name'];
                $contract_item->amount = $contract['amount'];
                $contract_item->start = $contract['start'];
                $contract_item->end = $contract['end'];
                $contract_item->progress = $contract['progress'];
                $contract_item->save();
            }

            $pledges = $request->pledges;
            foreach ($pledges as $pledge){
                $pledge_item = new Pledge();
                $pledge_item->facilities_id = $facilities_id;
                $pledge_item->type = $pledge['type'];
                $pledge_item->cost = $pledge['cost'];
                $pledge_item->description = $pledge['description'];
                $pledge_item->save();
            }

            $estates = $request->estates;
            foreach ($estates as $estate){
                $estate_item = new Estate();
                $estate_item->facilities_id = $facilities_id;
                $estate_item->type = $estate['type'];
                $estate_item->owner = $estate['owner'];
                $estate_item->cost = $estate['cost'];
                $estate_item->description = $estate['description'];
                $estate_item->save();
            }

            return response()->json([
                'success' => true,
            ], 201);
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function update(ApprovalsUpdateRequest $request)
    {
        try {
            $facilities_id = $request->facilities_id;

            if ($request->has('approvals')){

            }
            if ($request->has('contracts')){

            }
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data = Approvals::find($id);
            if ($data) {
                $data->delete();
                return response()->json([
                    'success' => true,
                ], 204);
            } else
                return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = Approvals::with('facilities')->find($id);
            if ($data){
                return response()->json(
                    $data,
                    200
                );
            }else
                return response()->json([], 404);
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
