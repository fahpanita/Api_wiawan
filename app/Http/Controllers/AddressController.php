<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
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
        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255',
            'street' => 'required',
            'district' => 'required',
            'province' => 'required',
            'zip_code' => 'required',
            'phone' => 'required',

        ], [
            "name.required" => "กรุณากรอกชื่อ",
            "street.required" => "กรุณาบ้านเลขที่/ซอย/ถนน",
            "district.required" => "กรุณากรอกตำบล/อำเภอ",
            "province.required" => "กรุณากรอกจังหวัด",
            "zip_code.required" => "กรุณากรอกรหัสไปรษณีย์",
            "phone.required" => "กรุณากรอกเบอร์โทร",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $address = new Address();
        $address->name = $data->name;
        $address->street = $data->street;
        $address->district = $data->district;
        $address->province = $data->province;
        $address->zip_code = $data->zip_code;
        $address->phone = $data->phone;
        $address->save();

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
            'street' => 'required',
            'district' => 'required',
            'province' => 'required',
            'zip_code' => 'required',
            'phone' => 'required',

        ], [
            "name.required" => "กรุณากรอกชื่อ",
            "street.required" => "กรุณาบ้านเลขที่/ซอย/ถนน",
            "district.required" => "กรุณากรอกตำบล/อำเภอ",
            "province.required" => "กรุณากรอกจังหวัด",
            "zip_code.required" => "กรุณากรอกรหัสไปรษณีย์",
            "phone.required" => "กรุณากรอกเบอร์โทร",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $address = Address::where('id', $id)->first();
        $address->name = $data->name;
        $address->street = $data->street;
        $address->district = $data->district;
        $address->province = $data->province;
        $address->zip_code = $data->zip_code;
        $address->phone = $data->phone;
        $address->update();

        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
    }


    public function destroy($id)
    {
        //
    }
}
