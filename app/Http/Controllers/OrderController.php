<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderProducts;
use App\Models\Orders;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
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
        $array_data = (array)$data;

        $dataAll = $request->json()->all();
        $products = $dataAll['product'];

        $validator = Validator::make($array_data, [
            'product.*.id' => 'required',
            'product.*.amount' => 'required',
            'product.*.price' => 'required',

        ], [
            "product.*.id" => "กรุณาเลือกสินค้า",
            "product.*.amount" => "กรุณากรอกจำนวน",
            "product.*.price" => "กรุณากรอกราคา",
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $datetime = new DateTime();

        $order = new Orders();
        $order->user_id = Auth::user()->id;
        $order->status = "Check Payment";
        $order->type_shipping = "Store";
        $order->order_date = $datetime->format('D-M-Y');



        if ($data->address) {
            $order->address = $data->address;
        }

        $order->save();

        foreach ($products as  $product) {

            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $product['id'];
            $order_item->amount = $product['amount'];
            $order_item->price = $product['price'];
            $order_item->save();
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
        //
    }


    public function destroy($id)
    {
        //
    }
}
