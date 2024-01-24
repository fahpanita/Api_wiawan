<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use KS\PromptPay;

class PaymentsController extends Controller
{

    public function index()
    {
        //
    }

    public function getPromptPay(Request $request)
    {
        // $totalWithShipping = $request->input('totalWithShipping');

        $data = json_decode($request->getContent());
        $array_data = (array)$data;

        $validator = Validator::make($array_data, [
            'order_id' => 'required|exists:orders,id',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        return response()->json(["message" => "บันทึกสำเร็จ"], 200);

        $pp = new PromptPay();
        $target = '088-656-5433';

        $totalPriceSum = OrderItem::where('order_id', '=', $request->input('order_id'))
            ->sum(DB::raw('price * amount'));

        $payload = $pp->generatePayload($target, $totalPriceSum);

        dd($payload);

        return response()->json(['payload' => $payload, 'amount' => $totalPriceSum]);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $array_data = (array) $data;

        $validator = Validator::make($array_data, [
            'order_id' => 'required|exists:orders,id',
            'slip_img' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $order_id = $request->input('order_id');

        $totalPriceSum = (float)OrderItem::where('order_id', $order_id)
            ->sum(DB::raw('price * amount'));

        // dd($totalPriceSum);

        $priceShiping = (float)DB::table('order_items')
            ->select('products.typeShipping')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', '=', $request->order_id)
            ->orderBy('products.typeShipping', 'desc')
            ->first()->typeShipping;


        $totalPrice = $totalPriceSum + $priceShiping;


        $payment = new Payment();
        $payment->user_id = Auth::id();
        $payment->order_id = $data->order_id;
        $payment->price = $totalPrice;
        $payment->status = "Paid";
        $payment->slip_img = $data->slip_img;
        $payment->save();


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
