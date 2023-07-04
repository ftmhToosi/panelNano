<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FinishRequest;
use App\Http\Requests\User\FinishUpdateRequest;
use App\Models\Finish;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FinishController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = Finish::with('facilities')->get();
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

    public function store(FinishRequest $request): JsonResponse
    {
        try {
            $data = new Finish();
            $data->facilities_id = $request->facilities_id;
            $data->name = $request->name;
            $data->amount = $request->amount;
            $data->title = $request->title;
            $data->supply = $request->supply;
            $file = $request->file('signature');
            $path = Storage::put('public/Facilities', $file);
            $data->signature = $path;
            $data->save();
            return response()->json([
                'success' => true,
            ], 201);
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function update(FinishUpdateRequest $request, $id)
    {
        try {
//            $data =
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data = Finish::find($id);
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
            $data = Finish::with('facilities')->find($id);
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
