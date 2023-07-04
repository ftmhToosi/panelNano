<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ProductRequest;
use App\Http\Requests\User\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = Product::with('facilities')->get();
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

    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $facilities_id = $request->facilities_id;

            $products = $request->products;
            foreach ($products as $product){
                $product_item = new Product();
                $product_item->facilities_id = $facilities_id;
                $product_item->name = $product['name'];
                $product_item->customer = $product['customer'];
                $product_item->specifications = $product['specifications'];
                $product_item->competitor = $product['competitor'];
                $product_item->sales_amount = $product['sales_amount'];
                $product_item->is_confirmation = $product['is_confirmation'];
                $product_item->save();
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

    public function update(ProductUpdateRequest $request, $id)
    {
        try {

        }catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data = Product::find($id);
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
            $data = Product::with('facilities')->find($id);
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
