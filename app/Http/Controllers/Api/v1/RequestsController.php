<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestUpdate;
use App\Http\Requests\User\DeleteRequest;
use App\Http\Requests\User\RequestsRequest;
use App\Models\Assessment;
use App\Models\Balance;
use App\Models\Bills;
use App\Models\Catalog;
use App\Models\CheckDoc;
use App\Models\Committee;
use App\Models\Credit;
use App\Models\ExpertAssignment;
use App\Models\Facilities;
use App\Models\Insurance;
use App\Models\Introduction;
use App\Models\Knowledge;
use App\Models\License;
use App\Models\Loans;
use App\Models\Place;
use App\Models\Proforma;
use App\Models\RegistrationDoc;
use App\Models\Report;
use App\Models\RequestDelete;
use App\Models\Requests;
use App\Models\Resume;
use App\Models\Signatory;
use App\Models\Statement;
use App\Models\User;
use App\Models\Warranty;
use App\Notifications\Notificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequestsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $data = Requests::with(['expert_assignment', 'warranty', 'facilities'])
                ->where('user_id', '=', $user->id)->get();

            return response()->json(
                $data,
                200
            );
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
//        return response()->json($request->file('file1')->extension());
            $data = new Requests();
            $data->user_id = $request->user_id;
            $data->type = $request->type;
            $data->shenaseh = rand(10000, 99999);
            $data->is_finished = false;
            $data->save();
            $request->validate((new RequestsRequest($data->id))->rules());

            if ($request->type == 'facilities'){
                $facilities = new Facilities();
                $facilities->request_id = $data->id;
                $facilities->title = $request->title;
                $facilities->type_f = $request->type_f;
                $facilities->save();

                $places = $request->places;
                foreach ($places as $place){
                    $place_item = new Place();
                    $place_item->facilities_id = $facilities->id;
                    $place_item->scope = $place['scope'];
                    $place_item->address = $place['address'];
                    $place_item->meterage = $place['meterage'];
                    $place_item->ownership = $place['ownership'];
                    $place_item->count = $place['count'];
                    $place_item->save();
                    $facilities->place()->save($place_item);
                }

                $introduction = new Introduction();
                $introduction->facilities_id = $facilities->id;
                $introduction->history = $request->history;
                $introduction->activity = $request->activity;
                $introduction->is_knowledge = $request->is_knowledge;
                $introduction->confirmation = $request->confirmation;
                $introduction->expiration = $request->expiration;
                $introduction->area = $request->area;
                $introduction->save();
                return response()->json([
                    'success' => true,
                    'last_id' => $facilities->id,
                ], 201);
            }

            else {
                $warranty = new Warranty();
                $warranty->request_id = $data->id;
                $warranty->title = $request->title;
                $warranty->type_w = $request->type_w;


                $file = $request->file('file1');
                $path = Storage::put('public/Warranty', $file);
                $warranty->file_name1 = $file->getClientOriginalName();
                $warranty->path1 = $path;

                $file2 = $request->file('file2');
                $path2 = Storage::put('public/Warranty', $file2);
                $warranty->file_name2 = $file2->getClientOriginalName();
                $warranty->path2 = $path2;

                $file3 = $request->file('file3');
                $path3 = Storage::put('public/Warranty', $file3);
                $warranty->file_name3 = $file3->getClientOriginalName();
                $warranty->path3 = $path3;
                $warranty->save();

                $licenses = $request->licenses;
                foreach ($licenses as $license) {
                    $license_item = new License();
                    $license_item->warranty_id = $warranty->id;
                    $files = $license['file'];
                    $path = Storage::put('public/Warranty', $files);
                    $license_item->file_name = $files->getClientOriginalName();
                    $license_item->path = $path;
                    $license_item->save();
                    $warranty->license()->save($license_item);
                }

                $registers = $request->register_doc;
                foreach ($registers as $register){
                    $register_item = new RegistrationDoc();
                    $register_item->warranty_id = $warranty->id;
                    $f = $register['file'];
                    $p = Storage::put('public/Warranty', $f);
                    $register_item->file_name = $f->getClientOriginalName();
                    $register_item->path = $p;
                    $register_item->save();
                    $warranty->registration_doc()->save($register_item);
                }

                $signatory = $request->signatory;
                foreach ($signatory as $item){
                    $signatory_item = new Signatory();
                    $signatory_item->warranty_id = $warranty->id;
                    $fil = $item['file'];
                    $pat = Storage::put('public/Warranty', $fil);
                    $signatory_item->file_name = $fil->getClientOriginalName();
                    $signatory_item->path = $pat;
                    $signatory_item->save();
                    $warranty->signatory()->save($signatory_item);
                }

                $knowledges = $request->knowledge;
                foreach ($knowledges as $knowledge){
                    $knowledge_item = new Knowledge();
                    $knowledge_item->warranty_id = $warranty->id;
                    $fileN = $knowledge['file'];
                    $pathN = Storage::put('public/Warranty', $fileN);
                    $knowledge_item->file_name = $fileN->getClientOriginalName();
                    $knowledge_item->path = $pathN;
                    $knowledge_item->save();
                    $warranty->knowledge()->save($knowledge_item);
                }

                $resume = $request->resume;
                foreach ($resume as $value){
                    $resume_item = new Resume();
                    $resume_item->warranty_id = $warranty->id;
                    $fileR = $value['file'];
                    $pathR = Storage::put('public/Warranty', $fileR);
                    $resume_item->file_name = $fileR->getClientOriginalName();
                    $resume_item->path = $pathR;
                    $resume_item->save();
                    $warranty->resume()->save($resume_item);
                }

                $loans = $request->loans;
                foreach ($loans as $loan){
                    $loans_item = new Loans();
                    $loans_item->warranty_id = $warranty->id;
                    $fileL = $loan['file'];
                    $pathL = Storage::put('public/Warranty', $fileL);
                    $loans_item->file_name = $fileL->getClientOriginalName();
                    $loans_item->path = $pathL;
                    $loans_item->save();
                    $warranty->loans()->save($loans_item);
                }

                $statements = $request->statements;
                foreach ($statements as $statement){
                    $statement_item = new Statement();
                    $statement_item->warranty_id = $warranty->id;
                    $fileS = $statement['file'];
                    $pathS = Storage::put('public/Warranty', $fileS);
                    $statement_item->file_name = $fileS->getClientOriginalName();
                    $statement_item->path = $pathS;
                    $statement_item->save();
                    $warranty->statement()->save($statement_item);
                }

                $balances = $request->balances;
                foreach ($balances as $balance){
                    $balance_item = new Balance();
                    $balance_item->warranty_id = $warranty->id;
                    $fileB = $balance['file'];
                    $pathB = Storage::put('public/Warranty', $fileB);
                    $balance_item->file_name = $fileB->getClientOriginalName();
                    $balance_item->path = $pathB;
                    $balance_item->save();
                    $warranty->balance()->save($balance_item);
                }

                $catalogs = $request->catalogs;
                foreach ($catalogs as $catalog){
                    $catalog_item = new Catalog();
                    $catalog_item->warranty_id = $warranty->id;
                    $fileC = $catalog['file'];
                    $pathC = Storage::put('public/Warranty', $fileC);
                    $catalog_item->file_name = $fileC->getClientOriginalName();
                    $catalog_item->path = $pathC;
                    $catalog_item->save();
                    $warranty->catalog()->save($catalog_item);
                }

                $insurances = $request->insurances;
                foreach ($insurances as $insurance){
                    $insurance_item = new Insurance();
                    $insurance_item->warranty_id = $warranty->id;
                    $fileI = $insurance['file'];
                    $pathI = Storage::put('public/Warranty', $fileI);
                    $insurance_item->file_name = $fileI->getClientOriginalName();
                    $insurance_item->path = $pathI;
                    $insurance_item->save();
                    $warranty->insurance()->save($insurance_item);
                }

                $invoices = $request->invoices;
                foreach ($invoices as $invoice){
                    $invoice_item = new Proforma();
                    $invoice_item->warranty_id = $warranty->id;
                    $fileIn = $invoice['file'];
                    $pathIn = Storage::put('public/Warranty', $fileIn);
                    $invoice_item->file_name = $fileIn->getClientOriginalName();
                    $invoice_item->path = $pathIn;
                    $invoice_item->save();
                    $warranty->proforma()->save($invoice_item);
                }

                $bills = $request->bills;
                foreach ($bills as $bill){
                    $bill_item = new Bills();
                    $bill_item->warranty_id = $warranty->id;
                    $fileBi = $bill['file'];
                    $pathBi = Storage::put('public/Warranty', $fileBi);
                    $bill_item->file_name = $fileBi->getClientOriginalName();
                    $bill_item->path = $pathBi;
                    $bill_item->save();
                    $warranty->bills()->save($bill_item);
                }

                return response()->json([
                    'success' => true,
                    'last_id' => $warranty->id,
                ], 201);
            }

        } catch (\Exception $exception) {
            return response()->json([
                'id' => $data->id,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function update(RequestUpdate $request)
    {

    }

    public function request_delete(DeleteRequest $request): JsonResponse
    {
        try {
            $sender = Auth::user();
            $data = new RequestDelete();
            $data->request_id = $request->request_id;
            $data->description = $request->description;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = Storage::put('public/DeleteRequest', $file);
                $data->file_name = $file->getClientOriginalName();
                $data->path = $path;
            }
            $data->save();
            $admin = User::where('type', '=', 'admin')->first();
            $requests = Requests::find($request->request_id);
            $message = 'یک درخواست حذف برای درخواست ' .$requests->shenaseh. ' دارید';
            $admin->notify(new Notificate($message, $sender->family, $request->request_id));
            return response()->json([
                'success' => true,
                'last_id' => $data->id,
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function get_request_delete(): JsonResponse
    {
        try {
            $data = RequestDelete::with(['request', 'request.user'])->get();
            return response()->json(
                $data,
                200
            );
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data = Requests::find($id);
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
            $data = Requests::with(['expert_assignment', 'facilities', 'facilities.introduction', 'facilities.shareholder',
                'facilities.part2', 'facilities.board', 'facilities.residence', 'facilities.manpower', 'facilities.educational',
                'facilities.place', 'facilities.product', 'facilities.bank', 'facilities.active_f', 'facilities.active_w',
                'facilities.benefit', 'facilities.asset', 'facilities.approvals', 'facilities.contract', 'facilities.pledge',
                'facilities.estate', 'facilities.finish',
                'warranty', 'warranty.license', 'warranty.registration_doc', 'warranty.signatory', 'warranty.knowledge',
                'warranty.resume', 'warranty.loans', 'warranty.statement', 'warranty.balance', 'warranty.catalog',
                'warranty.insurance', 'warranty.proforma', 'warranty.bills'
            ])->find($id);
            if ($data) {
                $responseItem = $data;

                $credit = Credit::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
                $commite = Committee::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
                $report = Report::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
                $assessment = Assessment::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
                $check = CheckDoc::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();

                switch (true) {
                    case $credit:
                        $responseItem['status'] = 'credit';
                        break;
                    case $commite:
                        $responseItem['status'] = 'committee';
                        break;
                    case $report:
                        $responseItem['status'] = 'report';
                        break;
                    case $assessment:
                        $responseItem['status'] = 'assessment';
                        break;
                    case $check:
                        $responseItem['status'] = 'check';
                        break;
                    default:
                        $responseItem['status'] = 'null';
                        break;
                }

                return response()->json(
                    $responseItem,
                    200
                );
            } else
                return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function view_request(): JsonResponse
    {
        try {
            $data = Requests::with(['user', 'expert_assignment', 'expert_assignment.expert'])->get();
            return response()->json(
                $data,
                200
            );
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function get_last_state(Request $request): JsonResponse
    {
        try {
            $data = Requests::find($request->request_id);

            if ($credit = Credit::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists()) {
                return response()->json(['status' => 'credit']);
            } elseif ($commite = Committee::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists()) {
                return response()->json(['status' => 'committee']);
            } elseif ($report = Report::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists()) {
                return response()->json(['status' => 'report']);
            } elseif ($assessment = Assessment::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists()) {
                return response()->json(['status' => 'assessment']);
            } elseif ($check = CheckDoc::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists()) {
                return response()->json(['status' => 'check']);
            } else {
                return response()->json(['status' => 'null']);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function get_current_request_user(): JsonResponse
    {
        try {
            $user = Auth::user();
            $data = Requests::with(['facilities','warranty', 'user'])
                ->where('user_id', '=', $user->id)
                ->where('is_finished', '=', false)->get();

            $response = [];

            foreach ($data as $item) {
                $responseItem = [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'type' => $item->type,
                    'shenaseh' => $item->shenaseh,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'user' => $item->user,
                    'facilities' => $item->facilities,
                    'warranty' => $item->warranty,
                ];

                $credit = Credit::query()->where('request_id', '=', $item->id)->where('is_accepted', '=', true)->exists();
                $commite = Committee::query()->where('request_id', '=', $item->id)->where('is_accepted', '=', true)->exists();
                $report = Report::query()->where('request_id', '=', $item->id)->where('is_accepted', '=', true)->exists();
                $assessment = Assessment::query()->where('request_id', '=', $item->id)->where('is_accepted', '=', true)->exists();
                $check = CheckDoc::query()->where('request_id', '=', $item->id)->where('is_accepted', '=', true)->exists();

                switch (true) {
                    case $credit:
                        $responseItem['status'] = 'credit';
                        break;
                    case $commite:
                        $responseItem['status'] = 'committee';
                        break;
                    case $report:
                        $responseItem['status'] = 'report';
                        break;
                    case $assessment:
                        $responseItem['status'] = 'assessment';
                        break;
                    case $check:
                        $responseItem['status'] = 'check';
                        break;
                    default:
                        $responseItem['status'] = 'null';
                        break;
                }
                $response[] = $responseItem;
            }

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function get_all_status($id): JsonResponse
    {
        try {
            $data = Requests::with('facilities')->find($id);

            $credit = Credit::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
            $commite = Committee::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
            $report = Report::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
            $assessment = Assessment::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
            $check = CheckDoc::query()->where('request_id', '=', $data->id)->where('is_accepted', '=', true)->exists();
//            $response = [
//                'data' => $data,
//                'credit' => $credit ? true : false,
//                'commite' => $commite ? true : false,
//                'report' => $report ? true : false,
//                'assessment' => $assessment ? true : false,
//                'check' => $check ? true : false
//            ];
            $data->credit = $credit ? true : false;
            $data->commite = $commite ? true : false;
            $data->report = $report ? true : false;
            $data->assessment = $assessment ? true : false;
            $data->check = $check ? true : false;

            return response()->json($data);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

//    public function get_warranty($id): JsonResponse
//    {
//        try {
//            $data = Warranty::with(['license', 'registration_doc', 'signatory', 'knowledge', 'resume',
//                'loans', 'statement', 'balance', 'catalog', 'insurance', 'proforma', 'bills'])
//                ->where('request_id' ,'=', $id)->get();
//            return response()->json(
//                $data,
//                200
//            );
//        }catch (\Exception $exception) {
//            return response()->json([
//                'success' => false,
//                'message' => $exception->getMessage()
//            ], 500);
//        }
//    }

    public function get_request_without_expert(): JsonResponse
    {
        try {
            $requests = ExpertAssignment::all();
            foreach ($requests as $request){
                $requests_id[] = $request->id;
            }
            $data = Requests::with(['facilities', 'warranty'])->whereNotIn('id',$requests_id)->get();
            return response()->json(
                $data,
                200
            );
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
