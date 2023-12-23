<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $totalWithShipping = $request->input('totalWithShipping');

        $pp = new PromptPay();
        $target = '088-656-5433';
        $amount = $totalWithShipping;

        $payload = $pp->generatePayload($target, $amount);

        return response()->json(['payload' => $payload, 'amount' => $amount]);
    }

    // public function getPromptPay($formattedTotal)
    // {
    //     $pp = new PromptPay();

    //     $target = '088-656-5433';
    //     $amount = $formattedTotal;

    //     return response()->json(['payload' => $pp->generatePayload($target, $amount)]);
    // }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $array_data = (array)$data;

        $validator = Validator::make($array_data, [
            'order_id' => 'required|exists:orders,id',
            'price' => 'required|numeric',
            'slip_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $order = Orders::find($request->input('order_id'));
        $order->status = "Paid";
        $order->save();

        $payment = new Payment();
        $payment->user_id = Auth::id();
        $payment->order_id = $data->order_id;
        $payment->price = $data->price;
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
