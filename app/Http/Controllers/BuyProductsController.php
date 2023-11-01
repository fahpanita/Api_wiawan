<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BuyProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuyProductsController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $array_data = (array)$data;
        $validator = Validator::make($array_data, [
            'amount' => 'required',

        ], [
            "amount.required" => "กรุณาใส่จำนวน",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $buyproducts = new BuyProducts();
        $buyproducts->amount = $data->amount;
        $buyproducts->save();

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
            'amount' => 'required',
        ], [
            "amount.required" => "กรุณาใส่จำนวน",
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $buyproducts = BuyProducts::where('id', $id)->first();
        $buyproducts->amount = $data->amount;
        $buyproducts->update();
        return response()->json(["message" => "แก้ไขสำเร็จ"], 200);
    }

    public function destroy($id)
    {
        //
    }
}
