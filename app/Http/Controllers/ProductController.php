<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductsCataories;
use App\Models\ProductsEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        // return response()->json(["message" => $data], 200);
        // $data = json_decode(file_get_contents('php://input'));
        // return response($data->categories_id);
        $array_data = (array)$data;
        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255',
            'price' => 'required',
            'picture' => 'required',
            'detailProduct' => 'required',
            'detailShipping' => 'required',
            'condition' => '',
            'categories_id' => 'required',
            'events_id' => 'required',
            'typeProduct' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $products = new Products();
        $products->name = $data->name;
        $products->price = $data->price;
        $products->picture = $data->picture;
        $products->detailProduct = $data->detailProduct;
        $products->detailShipping = $data->detailShipping;
        $products->condition = $data->condition;
        $products->typeProduct = $data->typeProduct;
        $products->save();

        // return response($data->categories_id);

        foreach ($data->categories_id as $catagory) {
            $productsCatagorirs = new ProductsCataories();
            $productsCatagorirs->product_id = $products->id;
            $productsCatagorirs->cataory_id = $catagory;
            $productsCatagorirs->save();
        }

        foreach ($data->events_id as $event) {

            $productsEvents = new ProductsEvents();
            $productsEvents->product_id = $products->id;
            $productsEvents->event_id = $event;

            $productsEvents->save();
        }
        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        $array_data = (array)$data;
        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255',
            'price' => 'required',
            'picture' => 'required',
            'detailProduct' => 'required',
            'detailShipping' => 'required',
            'condition' => '',
            'categories_id' => 'required',
            'events_id' => 'required',
            'typeProduct' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $products = Products::where('id', $id)->first();
        $products->name = $data->name;
        $products->price = $data->price;
        $products->picture = $data->picture;
        $products->detailProduct = $data->detailProduct;
        $products->detailShipping = $data->detailShipping;
        $products->condition = $data->condition;
        $products->categories_id = $data->categories_id;
        $products->events_id = $data->events_id;
        $products->typeProduct = $data->typeProduct;
        $products->update();
        return response()->json(["message" => "แก้ไขสำเร็จ"], 200);
    }


    public function destroy($id)
    {
        //
        if (!$id) {
            return response()->json(["message" => "ไม่พบ ID"], 400);
        }

        $products = Products::where('id', $id)->delete();
        return response()->json(["message" => "ลบสำเร็จ"], 200);
    }
}
