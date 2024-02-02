<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\OrderProducts;
use App\Models\Products;
use App\Models\ProductsCataories;
use App\Models\ProductsEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    public function index()
    {
        $products = Products::get();
        return response()->json($products, 200);
    }

    public function getId($id)
    {
        $products = Products::where('id', $id)->first();
        return response()->json($products, 200);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $array_data = (array)$data;

        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255',
            'price' => 'required',
            'stock' => 'required',
            'thumbnail' => 'required',
            'detailProduct' => 'required',
            'detailShipping' => 'required',
            'condition' => '',
            'categories_id' => 'required',
            'events_id' => 'required',
            'typeProduct' => 'required',
            'typeShipping' => 'required',

        ], [
            "name.required" => "กรุณากรอกชื่อ",
            "price.required" => "กรุณากรอกราคา",
            "stock.required" => "กรุณากรอกจำนวนสินค้า",
            "thumbnail.required" => "กรุณาใส่รูป",
            "detailProduct.required" => "กรุณากรอกรายละเอียดสินค้า",
            "detailShipping.required" => "กรุณากรอกรายละเอียดการจัดส่ง",
            "categories_id.required" => "กรุณาเลือกหมวดหมู่สินค้า",
            "events_id.required" => "กรุณาเลือกหมวดหมู่เทศกาล",
            "typeProduct.required" => "กรุณาเลือกประเภทสินค้า",
            "typeShipping.required" => "กรุณาเลือกประเภทการจัดส่ง",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $products = new Products();
        $products->name = $data->name;
        $products->price = $data->price;
        $products->stock = $data->stock;
        $products->thumbnail = $data->thumbnail;
        $products->detailProduct = $data->detailProduct;
        $products->detailShipping = $data->detailShipping;
        $products->condition = $data->condition;
        $products->typeProduct = $data->typeProduct;
        $products->typeShipping = $data->typeShipping;
        $products->save();


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

        $products->save();

        // $galleryImages = $request->file('gallery');

        // foreach ($galleryImages as $image) {
        //     // Save each image to the database
        //     $gallery = new Gallery();
        //     $gallery->product_id = $products->id;
        //     $gallery->name = $image->store('gallery', 'public');
        //     $gallery->save();
        // }

        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
    }

    public function show($id)
    {
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
            'stock' => 'required',
            'thumbnail' => 'required',
            'detailProduct' => 'required',
            'detailShipping' => 'required',
            'condition' => '',
            'categories_id' => 'required',
            'events_id' => 'required',
            'typeProduct' => 'required',
            'typeShipping' => 'required',
        ], [
            "name.required" => "กรุณากรอกชื่อ",
            "price.required" => "กรุณากรอกราคา",
            "stock.required" => "กรุณากรอกจำนวนสินค้า",
            "thumbnail.required" => "กรุณาใส่รูป",
            "detailProduct.required" => "กรุณากรอกรายละเอียดสินค้า",
            "detailShipping.required" => "กรุณากรอกรายละเอียดการจัดส่ง",
            "categories_id.required" => "กรุณาเลือกหมวดหมู่สินค้า",
            "events_id.required" => "กรุณาเลือกหมวดหมู่เทศกาล",
            "typeProduct.required" => "กรุณาเลือกประเภทสินค้า",
            "typeShipping.required" => "กรุณาเลือกประเภทการจัดส่ง",
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $products = Products::where('id', $id)->first();
        $products->name = $data->name;
        $products->price = $data->price;
        $products->stock = $data->stock;
        $products->thumbnail = $data->thumbnail;
        $products->detailProduct = $data->detailProduct;
        $products->detailShipping = $data->detailShipping;
        $products->condition = $data->condition;
        $products->categories_id = $data->categories_id;
        $products->events_id = $data->events_id;
        $products->typeProduct = $data->typeProduct;
        $products->typeShipping = $data->typeShipping;
        $products->update();
        return response()->json(["message" => "แก้ไขสำเร็จ"], 200);
    }


    public function destroy($id)
    {

        if (!$id) {
            return response()->json(["message" => "ไม่พบ ID"], 400);
        }

        $products = Products::where('id', $id)->delete();
        return response()->json(["message" => "ลบสำเร็จ"], 200);
    }
}
