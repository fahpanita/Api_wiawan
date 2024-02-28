<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function getEvent(Request $request)
    {
        $eventIds = $request->input('eventIds');

        $products = Products::select('products.*')
            ->join('products_events', 'products.id', '=', 'products_events.product_id')
            ->join(DB::raw("(WITH RECURSIVE events_tree  AS (
            SELECT id, name, parent_id
            FROM events
            WHERE id IN (" . implode(',', $eventIds) . ")  -- Use implode to convert array to string
            UNION
            SELECT c.id, c.name, c.parent_id
            FROM events c
            JOIN events_tree  ct ON c.parent_id = ct.id
          )
          SELECT * FROM events_tree ) as selected_events"), function ($join) {
                $join->on('selected_events.id', '=', 'products_events.event_id');
            })
            ->get();

        return response()->json(['data' => $products]);
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
