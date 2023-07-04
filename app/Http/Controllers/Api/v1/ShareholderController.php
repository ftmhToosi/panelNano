<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ShareholderRequest;
use App\Http\Requests\User\ShareholderUpdateRequest;
use App\Models\Board;
use App\Models\Educational;
use App\Models\Manpower;
use App\Models\Part2;
use App\Models\Residence;
use App\Models\Shareholder;
use Illuminate\Http\JsonResponse;

class ShareholderController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = Shareholder::with('facilities')->get();
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

    public function store(ShareholderRequest $request): JsonResponse
    {
        try {
            $facilities_id = $request->facilities_id;

            $shareholders = $request->shareholders;
            foreach ($shareholders as $shareholder){
                $shareholder_item = new Shareholder();
                $shareholder_item->facilities_id = $facilities_id;
                $shareholder_item->name = $shareholder['name'];
                $shareholder_item->type = $shareholder['type'];
                $shareholder_item->n_certificate = $shareholder['n_certificate'];
                $shareholder_item->n_national = $shareholder['n_national'];
                $shareholder_item->count = $shareholder['count'];
                $shareholder_item->percent = $shareholder['percent'];
                $shareholder_item->cost = $shareholder['cost'];
                $shareholder_item->education = $shareholder['education'];
                $shareholder_item->save();
            }

            $data = new Part2();
            $data->facilities_id = $facilities_id;
            $data->sum_count = $request->sum_count;
            $data->sum_percent = $request->sum_percent;
            $data->sum_cost = $request->sum_cost;
            $data->number = $request->number;
            $data->date = $request->date;
            $data->save();

            $boards = $request->boards;
            foreach ($boards as $board){
                $board_item = new Board();
                $board_item->facilities_id = $facilities_id;
                $board_item->name = $board['name'];
                $board_item->type = $board['type'];
                $board_item->position = $board['position'];
                $board_item->n_national = $board['n_national'];
                $board_item->birth_date = $board['birth_date'];
                $board_item->education = $board['education'];
                $board_item->study = $board['study'];
                $board_item->save();
            }

            $residences = $request->residences;
            foreach ($residences as $residence){
                $residence_item = new Residence();
                $residence_item->facilities_id = $facilities_id;
                $residence_item->name = $residence['name'];
                $residence_item->position = $residence['position'];
                $residence_item->address = $residence['address'];
                $residence_item->save();
            }

            $manpowers = $request->manpowers;
            foreach ($manpowers as $manpower){
                $manpower_item = new Manpower();
                $manpower_item->facilities_id = $facilities_id;
                $manpower_item->name = $manpower['name'];
                $manpower_item->position = $manpower['position'];
                $manpower_item->level_education = $manpower['level_education'];
                $manpower_item->study = $manpower['study'];
                $manpower_item->type_contract = $manpower['type_contract'];
                $manpower_item->work_experience = $manpower['work_experience'];
                $manpower_item->important = $manpower['important'];
                $manpower_item->save();
            }

            $educational = $request->educational;
            foreach ($educational as $value){
                $educational_item = new Educational();
                $educational_item->facilities_id = $facilities_id;
                $educational_item->name = $value['name'];
                $educational_item->university = $value['university'];
                $educational_item->study = $value['study'];
                $educational_item->position = $value['position'];
                $educational_item->records = $value['records'];
                $educational_item->save();
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

    public function update(ShareholderUpdateRequest $request, $id): JsonResponse
    {
        try {
            $data = Shareholder::find($id);
            if ($data){

                return response()->json([
                    'success' => true,
                ], 202);
            } else
                return response()->json([], 404);
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data = Shareholder::find($id);
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
            $data = Shareholder::with('facilities')->find($id);
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
