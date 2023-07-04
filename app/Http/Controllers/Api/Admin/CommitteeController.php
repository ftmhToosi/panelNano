<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommitteeRequest;
use App\Http\Requests\Admin\CommitteeUpdateRequest;
use App\Http\Requests\Admin\ConfirmCommitteeRequest;
use App\Models\Committee;
use App\Models\ExpertAssignment;
use App\Models\Requests;
use App\Models\User;
use App\Notifications\Notificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Kavenegar;

class CommitteeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = Committee::with(['request', 'request.user'])->get();
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

    public function store(CommitteeRequest $request): JsonResponse
    {
        try {
            $sender = Auth::user();
            $data = new Committee();
            $data->request_id = $request->request_id;
            $data->is_accepted = false;
            $file1 = $request->file('file1');
            $path1 = Storage::put('public/Committee', $file1);
            $data->file_name1 = $file1->getClientOriginalName();
            $data->path1 = $path1;

            $file2 = $request->file('file2');
            $path2 = Storage::put('public/Committee', $file2);
            $data->file_name2 = $file2->getClientOriginalName();
            $data->path2 = $path2;

            $file3 = $request->file('file3');
            $path3 = Storage::put('public/Committee', $file3);
            $data->file_name3 = $file3->getClientOriginalName();
            $data->path3 = $path3;
            $data->save();

            $admin = User::where('type', '=', 'admin')->first();
            $requests = Requests::find($request->request_id);
            $message = 'یک فایل کمیته برای درخواست ' .$requests->shenaseh. ' برای بررسی دارید';
            $admin->notify(new Notificate($message, $sender->family, $request->request_id));

            $receptor = $admin->phone;
            $token = 'مدیرمحترم';
            $token2 = null;
            $token3 = null;
            $template = "noticesAdmin";
            Kavenegar::VerifyLookup($receptor, $token, $token2, $token3, $template, $type = null);
            return response()->json([
                'success' => true,
                'last_id' => $data->id,
            ], 201);
        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function confirm_committee_admin(ConfirmCommitteeRequest $request, $id): JsonResponse
    {
        try {
            $data = Committee::find($id);
            if ($data){
                $data->is_accepted = $request->is_accepted;
                $user_id = ExpertAssignment::query()->where('requests_id', '=', $data->request_id)->first()->user2_id;
                $user = User::find($user_id);
                $sender = Auth::user();
                if ($request->is_accepted == false) {
                    $user->notify(new Notificate($request->message, $sender->family, $data->request_id));
                } else {
                    $requests = Requests::find($data->request_id);
                    $message = 'گزارش مرحله ارزیابی برای درخواست ' .$requests->shenaseh. ' تایید شد';
                    $user->notify(new Notificate($message, $sender->family, $data->request_id));
                }
                $data->save();

                $receptor = $user->phone;
                $token = $user->family;
                $token2 = null;
                $token3 = null;
                $template = "noticesExpert";
                Kavenegar::VerifyLookup($receptor, $token, $token2, $token3, $template, $type = null);
                return response()->json([
                    'message_notif' => $request->message,
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

    public function update(CommitteeUpdateRequest $request, $id): JsonResponse
    {
        try {
            $data = Committee::find($id);
            if ($data){
                $sender = Auth::user();
                if ($request->hasFile('file1')){
                    $file1 = $request->file('file1');
                    $path1 = Storage::put('public/Committee', $file1);
                    $data->file_name1 = $file1->getClientOriginalName();
                    $data->path1 = $path1;
                }
                if ($request->hasFile('file2')){
                    $file2 = $request->file('file2');
                    $path2 = Storage::put('public/Committee', $file2);
                    $data->file_name2 = $file2->getClientOriginalName();
                    $data->path2 = $path2;
                }
                if ($request->hasFile('file3')){
                    $file3 = $request->file('file3');
                    $path3 = Storage::put('public/Committee', $file3);
                    $data->file_name3 = $file3->getClientOriginalName();
                    $data->path3 = $path3;
                }
                $admin = User::where('type', '=', 'admin')->first();
                $requests = Requests::find($data->request_id);
                $message = ' فایل کمیته ویرایش شده برای درخواست ' .$requests->shenaseh. ' برای بررسی دارید';
                $admin->notify(new Notificate($message, $sender->family, $data->request_id));
                $data->save();

                $receptor = $admin->phone;
                $token = 'مدیرمحترم';
                $token2 = null;
                $token3 = null;
                $template = "noticesAdmin";
                Kavenegar::VerifyLookup($receptor, $token, $token2, $token3, $template, $type = null);
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
            $data = Committee::find($id);
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
            $data = Committee::with(['request', 'request.user'])->find($id);
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

    public function get_committee($id): JsonResponse
    {
        try {
            $data = Committee::select('id', 'is_accepted', 'request_id')->with(['request', 'request.user'])->where('request_id', '=', $id)->first();
            if ($data)
                return response()->json(
                    $data,
                    200
                );
            else
                return response()->json([
                    'message' => 'You do not have a Committee !!'
                ], 404);
        }catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function get_committee_for_admin($id): JsonResponse
    {
        try {
            $data = Committee::with(['request', 'request.user'])->where('request_id', '=', $id)->first();
            if ($data)
                return response()->json(
                    $data,
                    200
                );
            else
                return response()->json([
                    'message' => 'You do not have a Committee !!'
                ], 404);
        }catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
