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
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PaymentsController extends Controller
{

    public function index()
    {
        //
    }

    public function getPromptPay(Request $request)
    {

        $data = json_decode($request->getContent());
        $array_data = (array) $data;

        $validator = Validator::make($array_data, [
            'order_id' => 'required|exists:orders,id',
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

        $pp = new PromptPay();
        $target = '088-656-5433';
        $promptPay = $totalPrice;

        $payload = $pp->generatePayload($target, $promptPay);

        dd($payload);

        return response()->json(['payload' => $payload, 'amount' => $promptPay]);
    }
    // $payments = Payment::get();
    // $data = json_decode($request->getContent());
    // $array_data = (array) $data;
    // $validator = Validator::make($array_data, [
    //     'order_id' => 'required|exists:orders,id',
    //     'price' => 'required'
    // ]);

    // if ($validator->fails()) {
    //     return response()->json(["message" => $validator->errors()->first()], 400);
    // }

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

        $orderId = (string)$order_id;

        $token = env("LINE_CHANNAL_ACCECT_TOKEN");
        $url = "https://api.line.me/v2/bot/message/push";
        $responseNotify = Http::withHeaders([
            'Authorization' => "Bearer " . $token,
        ])->post(
            $url,
            [
                "to" => Auth::user()->line_id,
                "messages" => [
                    [
                        "type" => "flex",
                        "altText" => "This is a Flex Message",
                        "contents" => [
                            "type" => "bubble",
                            "hero" => [
                                "type" => "image",
                                "url" => "https://img2.pic.in.th/pic/Group-5392.png",
                                "size" => "full",
                                "aspectRatio" => "20:13",
                                "aspectMode" => "cover",
                                "action" => [
                                    "type" => "uri",
                                    "uri" => "http://linecorp.com/",
                                ],
                            ],
                            "body" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "คุณได้ชำระเงินแล้ว!",
                                        "size" => "xl",
                                        "weight" => "bold",
                                        "align" => "center",
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "กรุณารอยืนยันการชำระเงิน",
                                        "color" => "#999999",
                                        "align" => "center",
                                    ],
                                    ["type" => "separator"],
                                    [
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "margin" => "lg",
                                        "spacing" => "sm",
                                        "contents" => [
                                            [
                                                "type" => "box",
                                                "layout" => "baseline",
                                                "spacing" => "sm",
                                                "contents" => [
                                                    [
                                                        "type" => "text",
                                                        "text" => "หมายเลขคำสั่งซื้อ",
                                                        "color" => "#aaaaaa",
                                                        "size" => "sm",
                                                        "flex" => 4,
                                                    ],
                                                    [
                                                        "type" => "text",
                                                        "text" => $orderId,
                                                        "wrap" => true,
                                                        "color" => "#666666",
                                                        "size" => "sm",
                                                        "flex" => 5,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            "footer" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "spacing" => "sm",
                                "contents" => [
                                    [
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "contents" => [],
                                        "margin" => "sm",
                                    ],
                                ],
                                "flex" => 0,
                            ],
                        ],
                    ],
                ],
            ],

        );

        if ($responseNotify->failed()) {
            return response($responseNotify, 400);
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
