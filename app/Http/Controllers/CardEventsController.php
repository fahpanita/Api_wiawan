<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CardEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardEventsController extends Controller
{

    public function index()
    {
        $cardEvents = CardEvents::get();
        return response()->json($cardEvents, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //return response('123456');
        $data = json_decode($request->getContent());

        $array_data = (array)$data;

        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255',
            'thumbnail' => 'required',
            'detail' => 'required',

        ], [
            "name.required" => "กรุณากรอกชื่อเทศกาล",
            "thumbnail.required" => "กรุณาใส่รูปเทศกาล",
            "detail.required" => "กรุณาใส่รายละเอียดเทศกาล",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $cardEvents = new CardEvents();
        $cardEvents->name = $data->name;
        $cardEvents->thumbnail = $data->thumbnail;
        $cardEvents->detail = $data->detail;
        $cardEvents->save();

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
            'thumbnail' => 'required',
            'detail' => 'required',

        ], [
            "name.required" => "กรุณากรอกชื่อเทศกาล",
            "thumbnail.required" => "กรุณาใส่รูปเทศกาล",
            "detail.required" => "กรุณาใส่รายละเอียดเทศกาล",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $cardEvents = CardEvents::where('id', $id)->first();
        $cardEvents->name = $data->name;
        $cardEvents->thumbnail = $data->thumbnail;
        $cardEvents->detail = $data->detail;
        $cardEvents->update();

        return response()->json(["message" => "แก้ไขสำเร็จ"], 200);
    }


    public function destroy($id)
    {
        if (!$id) {
            return response()->json(["message" => "ไม่พบ ID"], 400);
        }

        $cardEvents = CardEvents::where('id', $id)->delete();
        return response()->json(["message" => "ลบสำเร็จ"], 200);
    }
}
