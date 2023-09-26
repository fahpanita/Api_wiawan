<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
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
        //สร้างเทศกาลหลัก
        $data = json_decode($request->getContent());
        // return response()->json(["message" => $data], 200);
        // $data = json_decode(file_get_contents('php://input'));
        $array_data = (array)$data;
        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $events = new Events();
        $events->name = $data->name;
        $events->save();
        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
    }

    public function storeSub(Request $request)
    {
        //สร้างเทศกาลย่อย
        $data = json_decode($request->getContent());
        $array_data = (array)$data;
        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255',
            'parent_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $events = new Events();
        $events->name = $data->name;
        $events->parent_id = $data->parent_id;
        $events->save();
        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
    }

    public function parentEvent()
    {
        //ดึงข้อมูลเทศกาลหลัก
        $results = Events::whereNull('parent_id')->with("sub")->get();
        return response()->json($results, 200);
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
        $data = json_decode($request->getContent());
        $array_data = (array)$data;
        $validator = Validator::make($array_data, [
            'name' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $event = Events::where('id', $id)->first();
        $event->name = $data->name;
        $event->update();
        return response()->json(["message" => "แก้ไขสำเร็จ"], 200);
    }

    public function destroy($id)
    {
        //
        if (!$id) {
            return response()->json(["message" => "ไม่พบ ID"], 400);
        }

        $event = Events::where('id', $id)->delete();

        return response()->json(["message" => "ลบสำเร็จ"], 200);
    }
}
